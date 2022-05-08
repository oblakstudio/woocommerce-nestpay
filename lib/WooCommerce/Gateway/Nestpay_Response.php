<?php //phpcs:disable WordPress.Security.NonceVerification.Missing
/**
 *  Nestpay_Response class file
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage Gateway
 */

namespace Oblak\NPG\WooCommerce\Gateway;

use Oblak\NPG\WooCommerce\Gateway\Nestpay_Gateway as WC_Gateway_NestPay;
use Oblak\NPG\WooCommerce\Data\Nestpay_Transaction;
use WC_Order;

/**
 * NestPay Response handler
 *
 * @since 1.0.0
 */
class Nestpay_Response {

    /**
     * Merchant ID
     *
     * Used for hash validation
     *
     * @var string
     */
    private $merchant_id;

    /**
     * Store key
     *
     * Used for hash validation
     *
     * @var string
     */
    private $store_key;

    /**
     * Class constructor
     *
     * @param string $merchant_id  Merchant ID.
     * @param string $store_key    Store Key.
     */
    public function __construct( $merchant_id, $store_key ) {
        $this->merchant_id = $merchant_id;
        $this->store_key   = $store_key;

        add_action( 'woocommerce_api_nestpay', array($this, 'check_response') );
        add_action( 'valid_nestpay_response', array($this, 'valid_response') );
        add_action( 'woocommerce_before_thankyou', array($this, 'before_thankyou_nestpay') );
        add_action( 'woocommerce_thankyou_nestpay', array($this, 'thankyou_nestpay_details') );
        add_filter( 'woocommerce_thankyou_order_received_text', array($this, 'thankyou_text'), 99, 2 );
    }

    /**
     * Checks if the response actually came from nestpay.
     *
     * @param Nestpay_Transaction $transaction Transaction object.
     * @return boolean                    True if the response is valid, false if not
     */
    private function validate_hash( $transaction ) {
        $hash_params = array_map( function ( $prop ) use ( $transaction ) {
            return $transaction->{"get_$prop"}();
        }, explode( '|', $transaction->get_HASHPARAMS() ));

        $hash_params_string = implode( '|', $hash_params ) . '|' . $this->store_key;

        //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
        $hashed = base64_encode( pack( 'H*', hash( 'sha512', $hash_params_string ) ) );

        return $hashed === $transaction->get_HASH();
    }

    /**
     * Response handler function
     */
    public function check_response() {
        if ( empty( $_POST ) ) {
            wp_die( 'POST should not be empty' );
        }

        $data = wp_unslash( $_POST );

        // Set order ID to order ID until we implement the sequenatial order IDs.
        $data['order_id'] = $this->get_order_id_by_oid( $data['oid'] );
        $data['clientid'] = $this->merchant_id;

        $transaction = wcnpg_get_transaction_by_id( $data['TransId'] );

        if ( ! $transaction ) {
            $transaction = new Nestpay_Transaction( $data );
            $transaction->save();
        }

        if ( $this->validate_hash( $transaction ) ) {
            do_action( 'valid_nestpay_response', $transaction );
        }

        WC_Gateway_NestPay::log( 'NestPay signature invalid', 'critical' );

        exit;

    }

    /**
     * Retrieve order by oid
     *
     * @param  string $oid NestPay OID.
     * @return int         Order ID.
     */
    private function get_order_id_by_oid( $oid ) {
        return apply_filters( 'woocommerce_nestpay_callback_order_id', $oid );
    }

    /**
     * Callback for valid NestPay response
     *
     * @param Nestpay_Transaction $transaction Transaction object.
     */
    public function valid_response( Nestpay_Transaction $transaction ) {
        $order = wc_get_order( $transaction->get_order_id() );

        if ( ! $order ) {
            WC_Gateway_NestPay::log( 'NestPay: Order not found', 'critical' );
            // $this->invalidOrder( $transaction );
            return;
        }

        WC_Gateway_NestPay::log( 'Found order #' . $order->get_order_number(), 'debug' );
        WC_Gateway_NestPay::log( 'Transaction status: ' . $transaction->get_ProcReturnCode(), 'debug' );

        switch ( $transaction->get_ProcReturnCode() ) {
            case '00':
                $this->handle_transaction_approved( $order, $transaction );
                break;
            default:
                $this->handle_transaction_declined( $order, $transaction );
                break;
        }
    }

    /**
     * Handles a declined transaction
     *
     * @param  WC_Order            $order       Order object.
     * @param  Nestpay_Transaction $transaction Transaction Object.
     */
    private function handle_transaction_declined( $order, $transaction ) {
        $this->payment_failed( $order, $transaction );
    }

    /**
     * Handles approved transaction.
     *
     * @param  WC_Order            $order       Order Object.
     * @param  Nestpay_Transaction $transaction Transaction Object.
     */
    private function handle_transaction_approved( $order, $transaction ) {
        if ( $order->has_status( wc_get_is_paid_statuses() ) ) {
            WC_Gateway_NestPay::log( 'Aborting, Order #' . $order->get_order_number() . ' is already complete' );
        }

        $this->payment_complete( $order, $transaction );
    }

    /**
     * Finalizes the payment and redirects to the success page.
     *
     * @param  WC_Order            $order       Order Object.
     * @param  Nestpay_Transaction $transaction Transaction Object.
     */
    private function payment_complete( $order, $transaction ) {

        if ( $order->has_status( array('processing', 'completed') ) ) {
            $this->redirect_to_thankyou( $order );
        }

        $order_note = self::generate_order_note( $transaction );

        $order->add_order_note( $order_note );

        $this->save_nestpay_data( $order, $transaction );

        $order->payment_complete( $transaction->get_TransId() );

        do_action( 'woocommerce_nestpay_payment_complete', $order, $transaction );

        $this->redirect_to_thankyou( $order );

    }

    /**
     * Voids the payment and redirects to payment failed page
     *
     * @param  WC_Order            $order       Order Object.
     * @param  Nestpay_Transaction $transaction Transaction Object.
     */
    private function payment_failed( $order, $transaction ) {

        if ( $order->has_status( array('completed') ) ) {
            $this->redirect_to_thankyou( $order );
        }

        $order_note = self::generate_order_note( $transaction );

        $order->add_order_note( $order_note );
        $this->save_nestpay_data( $order, $transaction );

        $order->update_status( 'failed', __( 'Payment failed', 'woocommerce-nestpay' ) );

        WC()->cart->empty_cart();

        WCNPG()->client->void_payment( $order );

        $this->redirect_to_thankyou( $order );

    }

    /**
     * Redirect to thank you page after processing payment
     *
     * @param  WC_Order $order Order object.
     */
    private function redirect_to_thankyou( $order ) {
        wp_safe_redirect( $order->get_checkout_order_received_url() );
        exit;
    }

    /**
     * Outputs the before thank you content.
     *
     * @param int $order_id Order ID.
     */
    public function before_thankyou_nestpay( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order->has_status( 'failed' ) ) {
            return;
        }

        $transaction = new Nestpay_Transaction( $order->get_meta( '_nestpay_callback_id', true ) );

        if ( '00' !== $transaction->get_ProcReturnCode() ) {
            echo '<style>.woocommerce-thankyou-order-failed { display: none !important; }</style>';
            esc_html_e( 'Transaction failed. Your payment card is not charged.', 'woocommerce-nestpay' );
        }
    }

    /**
     * Displays the NestPay transaction message on the thank you page
     *
     * @param  string   $text  The default text.
     * @param  WC_Order $order The order object.
     * @return string          The new text.
     */
    public function thankyou_text( string $text, WC_Order $order ) {

        if ( $order->get_payment_method() !== 'nestpay' ) :
            return $text;
        endif;

        $transaction = new Nestpay_Transaction( $order->get_meta( '_nestpay_callback_id', true ) );

        if ( 0 === $transaction->get_id() ) {
            return $text . '<br>' . esc_html__( 'NestPay response unknown', 'woocommerce-nestpay' );
        }

        return $text . '<br>' . esc_html__( 'Your payment card has been successfully charged', 'woocommerce-nestpay' );

    }

    /**
     * Displays the NestPay transaction details on the order page
     *
     * @param int $order_id Order ID.
     */
    public function thankyou_nestpay_details( $order_id ) {

        $order       = wc_get_order( $order_id );
        $transaction = new Nestpay_Transaction( $order->get_meta( '_nestpay_callback_id', true ) );

        if ( 0 === $transaction->get_id() ) {
            return;
        }

        $fields = wcnpg_get_user_transaction_fields();

        printf(
            '<h2 style="text-align: center">%s</h2>',
            esc_html__( 'Transaction details', 'woocommerce-nestpay' )
        );

        // Chunk the fields into groups of 5.
        foreach ( array_chunk( $fields, 5, true ) as $row ) {

            echo '<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">';

            foreach ( $row as $field => $label ) {
                $value = $transaction->{"get_$field"}();
                printf(
                    '<li>%s <strong>%s</strong></li>',
                    esc_html( $label ),
                    '' !== $value ? esc_html( $value ) : '/'
                );
            }

            echo '</ul>';

        }

    }

    /**
     * Saves NestPay specific data to the order
     *
     * Saves complete NestPay response, sets the responded flag and removed the check counter
     *
     * @param  WC_Order            $order       Order Object.
     * @param  Nestpay_Transaction $transaction Transaction Object.
     */
    private function save_nestpay_data( &$order, $transaction ) {
        $nestpay_status = 'failed';

        if ( '00' === $transaction->get_ProcReturnCode() ) {
            $nestpay_status = 'PreAuth' === $transaction->get_trantype() ? 'reserved' : 'charged';
        }

        $order->add_meta_data( '_nestpay_status', $nestpay_status, true );
        $order->add_meta_data( '_nestpay_callback_id', $transaction->get_id(), true );
        $order->save();
    }

    /**
     * Generates the order note for the transaction
     *
     * @param  Nestpay_Transaction $transaction Transaction object.
     */
    public static function generate_order_note( $transaction ) {

        $transaction_status = __( 'Declined', 'woocommerce-nestpay' );

        if ( '00' === $transaction->get_ProcReturnCode() ) {

            $transaction_status = __( 'Approved', 'woocommerce-nestpay' ) . ' - ';

            $transaction_status .= ( 'PreAuth' === $transaction->get_trantype() )
                ? __( 'Funds reserved', 'woocommerce-nestpay' )
                : __( 'Funds deposited', 'woocommerce-nestpay' );

        }

        return sprintf(
            '<h4>%s</h4>
            <p>
                <strong>%s</strong>: %s<br>
                <strong>%s</strong>: %s<br>
                <strong>%s</strong>: %s<br>
                <strong>%s</strong>: %s<br>
                <strong>%s</strong>: %s<br>
            </p>',
            __( 'NestPay payment status', 'woocommerce-nestpay' ),
            __( 'Transaction date', 'woocommerce-nestpay' ),
            gmdate( 'd. m. Y - H:i', strtotime( $transaction->get_EXTRA_TRXDATE() ) ),
            __( 'Transaction status', 'woocommerce-nestpay' ),
            $transaction_status,
            __( 'Transaction amount', 'woocommerce-nestpay' ),
            $transaction->get_amount() . ' ' . wcnpg_get_currencies()[ $transaction->get_currency() ],
            __( 'Status code', 'woocommerce-nestpay' ),
            $transaction->get_ProcReturnCode(),
            __( 'Authorization code', 'woocommerce-nestpay' ),
            $transaction->get_AuthCode(),
        );
    }

}
