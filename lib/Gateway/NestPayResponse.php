<?php
namespace Oblak\NPG\Gateway;

use Oblak\NPG\Gateway\NestPayGateway as WC_Gateway_NestPay;
use WC_Order;

use function Oblak\NPG\getGatewayCurrencies;

class NestPayResponse {

    private static $statuses = [
        '00' => 'Approved',
        '99' => 'Error',
    ];

    /**
     *
     * @var null|array NestPay response fields
     */
    // private static $response_fields = null;

    private $merchant_id;

    private $api_url;

    private $api_username;

    private $api_password;

    private $store_key;

    /**
     * Class constructor
     *
     * @param string $merchant_id  Merchant ID
     * @param string $store_key    Store Key
     * @param string $api_url      API url
     * @param string $api_username API username
     * @param string $api_password API password
     */
    public function __construct($merchant_id, $store_key, $api_url, $api_username, $api_password)
    {

        // if (is_null(self::$response_fields)) :
        //     self::$response_fields = getResponseFields();
        // endif;

        $this->merchant_id  = $merchant_id;
        $this->store_key    = $store_key;

        $this->api_url      = $api_url;
        $this->api_username = $api_username;
        $this->api_password = $api_password;

        add_action('woocommerce_api_nestpay', [$this, 'checkResponse']);
        add_action('valid_nestpay_response', [$this, 'validResponse']);
        add_action('woocommerce_thankyou_nestpay',[$this, 'thankYouNestPayDetails']);
        add_filter('woocommerce_thankyou_order_received_text', [$this, 'thankYouText'], 99, 2);
    }

    /**
     * @deprecated 1.0
     * @return boolean
     */
    private function validateHash()
    {

        $response = wp_unslash($_POST);

        $response_hash = $response['HASH'];

        $hash_template = 'clientid|oid|AuthCode|ProcReturnCode|Response|mdStatus|cavv|eci|md|rnd';

        $codes = [
            'clientid'       => $this->merchant_id,
            'oid'            => $response['oid'],
            'AuthCode'       => $response['AuthCode'],
            'ProcReturnCode' => $response['ProcReturnCode'],
            'Response'       => $response['Response'],
            'mdStatus'       => $response['mdStatus'],
            'cavv'           => $response['cavv'],
            'eci'            => $response['eci'],
            'md'             => $response['md'],
            'rnd'            => $response['rnd'],
        ];

        $string = strtr($hash_template, $codes);
        $hashed = hash('sha512', $string);
        $packed = base64_encode(pack('H*', $hashed));

        return true;

    }

    public function checkResponse()
    {

        if ( !empty($_POST) && $this->validateHash()) :

            $response = new GwResponseDTO($_POST);

            do_action('valid_nestpay_response', $response);

        endif;

        WC_Gateway_NestPay::log('NestPay signature invalid', 'critical');

        exit;

    }

    public function validResponse(GwResponseDTO $response)
    {

        $order = !empty($response->oid) ? wc_get_order($response->oid) : false;

        WC_Gateway_NestPay::log('NestPay response is: '. json_encode($response), 'debug');

        if (!$order) :
            // $this->invalidOrder($response);
            return;
        endif;


        // Check if response status is approved or error (00 or 99)
        $status = (array_key_exists($response->ProcReturnCode, self::$statuses) )
            ? self::$statuses[$response->ProcReturnCode]  // If it is, set the status
            : 'Declined';                                 // If not, transaction has been declined

        WC_Gateway_NestPay::log('Found order #' . $order->get_id());
        WC_Gateway_NestPay::log('Transaction status: ' . $status);

        call_user_func([&$this, "handleOrder{$status}"], $order, $response);

    }

    /**
     *
     * @param  WC_Order $order
     * @param  array    $response
     */
    private function handleOrderError($order, $response)
    {

        if ($order->has_status('cancelled')) :
            // $this->payCancelledOrder($order, $response);
        endif;

        $this->paymentFailed($order, $response);

    }

    /**
     *
     * @param  WC_Order $order
     * @param  array    $response
     */
    private function handleOrderDeclined($order, $response)
    {

        
        if ($order->has_status('cancelled')) :
            // $this->payCancelledOrder($order, $response);
        endif;

        $this->paymentFailed($order, $response);

        die;

    }

    /**
     *
     * @param  WC_Order $order
     * @param  array    $response
     */
    private function handleOrderApproved($order, $response) {

        if ( $order->has_status( wc_get_is_paid_statuses()) ) :
            WC_Gateway_NestPay::log('Aborting, Order #' . $order->get_id() . ' is already complete');
        endif;

        $this->paymentComplete($order, $response);

    }

    /**
     *
     * @param  WC_Order $order
     * @param  GwResponseDTO    $response
     */
    private function paymentComplete($order, $response)
    {

        if ( $order->has_status(['processing', 'completed']) ) {
            $this->redirectToThankYou($order);
        }
            
        $order_note = $this->generateOrderNote($response);
        
        $order->add_order_note($order_note);

        $this->saveNestpayData($order, $response);

        $order->payment_complete($response->TransId);

        $this->redirectToThankYou($order);

    }

    /**
     *
     * @param  WC_Order    $order
     * @param  GwResponseDTO $response
     */
    private function paymentFailed($order, $response)
    {

        if ( $order->has_status(['completed']) ) :
            $this->redirectToThankYou($order);
        endif;

        $order_note = $this->generateOrderNote($response);

        $order->add_order_note($order_note);
        $this->saveNestPayData($order, $response);

        $order->update_status('failed', __('Payment failed', 'woocommerce-nestpay'));

        WC()->cart->empty_cart();

        $this->voidTransaction($order->get_id());

        $this->redirectToThankYou($order);

    }

    /**
     * Redirect to thank you page after processing payment
     *
     * @param  WC_Order $order
     * @return void
     */
    private function redirectToThankYou($order)
    {
        wp_safe_redirect($order->get_checkout_order_received_url());
        exit;
    }

    /**
     * Displays the NestPay transaction details on the order page
     * 
     * @param int $order_id Order ID
     */
    public function thankYouNestPayDetails(int $order_id) : void {

        $order  = wc_get_order($order_id);
        $meta   = $order->get_meta('_nestpay_response', true);

        if (!$meta instanceof GwResponseDTO) {
            return;
        }

        $fields = [
            'Response'         => __('Transaction status', 'woocommerce-nestpay'),
            'TransId'          => __('Transaction ID', 'woocommerce-nestpay'),
            'ProcReturnCode'   => __('Status code', 'woocommerce-nestpay'), 
            'AuthCode'         => __('Authorization code', 'woocommerce-nestpay'),
            'mdStatus'         => __('3D Status', 'woocommerce-nestpay'),
            'maskedCreditCard' => __('Payment card number', 'woocommerce-nestpay'),
            // 'instalment'       => __('Installments', 'woocommerce-nestpay'),
        ];

        printf(
            '<h2 style="text-align: center">%s</h2>',
            __('Transaction details', 'woocommerce-nestpay')
        );

        // Chunk the fields into groups of 5
        foreach ( array_chunk($fields, 5, true) as $row) {

            echo '<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">';

            foreach ($row as $field => $label ) {

                printf(
                    '<li>%s <strong>%s</strong></li>',
                    $label,
                    $meta->$field == '' ? '/' : $meta->$field,
                );

            }

            echo '</ul>';

        }

    }
    /**
     * Displays the NestPay transaction message on the thank you page
     * 
     * @param  string   $text
     * @param  WC_Order $order
     */
    public function thankYouText(string $text, WC_Order $order) {

        if ( $order->get_payment_method() !== 'nestpay') :
            return $text;
        endif;

        /** @var GwResponseDTO $response */
        $response  = $order->get_meta('_nestpay_response');

        if (!$response instanceof GwResponseDTO) {
            return $text. __('NestPay response unknown', 'woocommerce-nestpay');
        }


        if ( $response->ProcReturnCode != '00' ) {
            return $text . '<br>' .  __('Transaction failed. Your payment card is not charged.');
        }

        return ($response->trantype == 'PreAuth')
            ? $text . '<br>' . __('The order amount has been successfully reserved on your payment card.', 'woocommerce-nestpay')
            : $text . '<br>' . __('Your payment card has been successfully charged', 'woocommerce-nestpay');

    }

    /**
     * Saves NestPay specific data to the order
     * 
     * Saves complete NestPay response, sets the responded flag and removed the check counter
     * 
     * @param  WC_Order    $order
     * @param  GwResponseDTO $response
     */
    private function saveNestPayData(&$order, $response) {

        // Transaction info
        $order->add_meta_data('_nestpay_response', $response, true);
        $order->add_meta_data('_nestpay_transaction', $response->trantype, true);

        // Response info
        $order->add_meta_data('_nestpay_responded', 1, true);
        $order->delete_meta_data('_nestpay_responded_check');

        $order->save();

    }

    private function generateOrderNote(GwResponseDTO $response) : string {

        $transaction_status = __('Declined', 'woocommerce-nestpay');

        if ($response->ProcReturnCode == '00') {

            $transaction_status = __('Approved', 'woocommerce-nestpay') . ' - ';
        
            $transaction_status .= ($response->trantype == 'PreAuth')
                ? __('Funds reserved', 'woocommerce-nestpay')
                : __('Funds deposited', 'woocommerce-nestpay');

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
            __('NestPay payment status', 'woocommerce-nestpay'),
            __('Transaction date', 'woocommerce-nestpay'),
            date('d. m. Y - H:i', strtotime($response->EXTRA_TRXDATE)),
            __('Transaction status', 'woocommerce-nestpay'),
            $transaction_status,
            __('Transaction amount', 'woocommerce-nestpay'),
            $response->amount . ' ' . getGatewayCurrencies()[$response->currency],
            __('Status code', 'woocommerce-nestpay'),
            $response->ProcReturnCode,
            // __('Transaction ID', 'woocommerce-nestpay'),
            // $response->TransId,
            __('Authorization code', 'woocommerce-nestpay'),
            $response->AuthCode,
        );

    }

    private function voidTransaction($order_id)
    {

        /** @var WC_Gateway_NestPay $gateway */
        $gateway = wc_get_payment_gateway_by_order($order_id);

        $gateway->void_payment($order_id);

    }

}
