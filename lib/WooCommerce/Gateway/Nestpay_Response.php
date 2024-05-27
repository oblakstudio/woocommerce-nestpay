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

        add_action( 'woocommerce_api_nestpay', array( $this, 'check_response' ) );
        add_action( 'valid_nestpay_response', array( $this, 'valid_response' ) );
        add_action( 'woocommerce_before_thankyou', array( $this, 'before_thankyou_nestpay' ) );
        add_action( 'woocommerce_thankyou_nestpay', array( $this, 'thankyou_nestpay_details' ), 9 );
        add_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'thankyou_text' ), 99, 2 );
    }

    /**
     * Checks if the response actually came from nestpay.
     *
     * @param Nestpay_Transaction $transaction Transaction object.
     * @return boolean                    True if the response is valid, false if not
     */
    private function validate_hash( $transaction ) {
        $hash_params = array_map(
            function ( $prop ) use ( $transaction ) {
                return $transaction->{"get_$prop"}();
            },
            explode( '|', $transaction->get_HASHPARAMS() )
        );

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

        $transaction = nestpay_get_transaction( nestpay_get_transaction_id_by_transid( $data['TransId'] ?? '' ) );
        $transaction = $transaction ?: new Nestpay_Transaction( $data ); // phpcs:ignore Universal.Operators.DisallowShortTernary.Found -- ELVIS LIVES.

        $transaction->save();

        if ( $this->validate_hash( $transaction ) ) {
            /**
             * Fired for valid NestPay response
             *
             * @param Nestpay_Transaction $transaction Transaction object.
             * @since 2.0.0
             */
            do_action( 'valid_nestpay_response', $transaction );
        }

        WC_Gateway_NestPay::log( 'NestPay signature invalid', 'critical' );

        wp_die( esc_html__( 'NestPay signature invalid', 'wc-serbian-nestpay' ) );
    }

    /**
     * Retrieve order by oid
     *
     * @param  string $oid NestPay OID.
     * @return int         Order ID.
     */
    private function get_order_id_by_oid( $oid ) {
        /**
         * Filters the order ID retrieved from NestPay OID
         *
         * @param  int $oid NestPay OID.
         * @return int      Order ID.
         *
         * @since 2.0.0
         */
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
        if ( $order->has_status( array( 'processing', 'completed' ) ) ) {
            $this->redirect_to_thankyou( $order );
        }

        $order_note = self::generate_order_note( $transaction );

        $order->add_order_note( $order_note );

        $this->save_nestpay_data( $order, $transaction );

        $order->payment_complete( $transaction->get_TransId() );

        /**
         * Fires after a NestPay payment is completed
         *
         * @param WC_Order            $order       Order Object.
         * @param Nestpay_Transaction $transaction Transaction Object.
         *
         * @since 2.0.0
         */
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
        if ( $order->has_status( array( 'completed' ) ) ) {
            $this->redirect_to_thankyou( $order );
        }

        $order_note = self::generate_order_note( $transaction );

        $order->add_order_note( $order_note );
        $this->save_nestpay_data( $order, $transaction );

        $order->update_status( 'failed', __( 'Payment failed', 'wc-serbian-nestpay' ) );

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

        $transaction = nestpay_get_transaction( $order );

        if ( '00' !== $transaction->get_ProcReturnCode() ) {
            echo '<' . 'style>.woocommerce-thankyou-order-failed { display: none !important; }</style>'; //phpcs:ignore
            esc_html_e( 'Transaction failed. Your payment card is not charged.', 'wc-serbian-nestpay' );
        }
    }

    /**
     * Displays the NestPay transaction message on the thank you page
     *
     * @param  string         $text  The default text.
     * @param  WC_Order|false $order The order object.
     * @return string                The new text.
     */
    public function thankyou_text( string $text, WC_Order|false $order ) {
        if ( ! $order || $order->get_payment_method() !== 'nestpay' ) :
            return $text;
        endif;

        $transaction = nestpay_get_transaction( $order );

        if ( 0 === $transaction->get_id() ) {
            return $text . esc_html__( 'NestPay response unknown', 'wc-serbian-nestpay' );
        }

        return $text . ' ' . esc_html__( 'Your payment card has been successfully charged', 'wc-serbian-nestpay' );
    }

    /**
     * Displays the NestPay transaction details on the order page
     *
     * @param int $order_id Order ID.
     */
    public function thankyou_nestpay_details( $order_id ) {
        $tx = nestpay_get_transaction( wc_get_order( $order_id ) );

        if ( 0 === $tx->get_id() || $tx->get_ProcReturnCode() !== '00' ) {
            return;
        }

        printf(
            '<h2 class="woocommerce-order-details__title">%s</h2>',
            esc_html__( 'Transaction details', 'wc-serbian-nestpay' )
        );

        add_filter( 'woocommerce_nestpay-transaction_get_EXTRA_TRXDATE', fn( $d ) => $d?->date_i18n( wc_date_format() . ' ' . wc_time_format() ) ?? 'N/a' );

        // Chunk the fields into groups of 5.
        foreach ( array_chunk( nestpay_get_transaction_fields(), 5, true ) as $row ) {

            echo '<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">';

            foreach ( $row as $field => $label ) {
                $value = $tx->{"get_$field"}();
                printf(
                    '<li>%s <strong>%s</strong></li>',
                    esc_html( $label ),
                    ! empty( $value ) ? esc_html( $value ) : '/'
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
     * @param  Nestpay_Transaction $tx Transaction object.
     */
    public static function generate_order_note( $tx ) {
        $tx_msg = __( 'Declined', 'wc-serbian-nestpay' );

        if ( '00' === $tx->get_ProcReturnCode() ) {
            $tx_msg  = __( 'Approved', 'wc-serbian-nestpay' ) . ' ';
            $tx_msg .= 'PreAuth' === $tx->get_trantype()
                ? __( 'Funds reserved', 'wc-serbian-nestpay' )
                : __( 'Funds deposited', 'wc-serbian-nestpay' );
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
            __( 'NestPay payment status', 'wc-serbian-nestpay' ),
            __( 'Transaction date', 'wc-serbian-nestpay' ),
            gmdate( 'd. m. Y - H:i', strtotime( $tx->get_EXTRA_TRXDATE() ) ),
            __( 'Transaction status', 'wc-serbian-nestpay' ),
            trim( $tx_msg ),
            __( 'Transaction amount', 'wc-serbian-nestpay' ),
            $tx->get_amount() . ' ' . get_woocommerce_currency(),
            __( 'Status code', 'wc-serbian-nestpay' ),
            $tx->get_ProcReturnCode(),
            __( 'Authorization code', 'wc-serbian-nestpay' ),
            $tx->get_AuthCode(),
        );
    }
}
