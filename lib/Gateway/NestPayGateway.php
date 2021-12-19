<?php

namespace Oblak\NPG\Gateway;

use WC_Logger;
use WC_Order;
use WC_Payment_Gateway;
use Automattic\Jetpack\Constants;

use Oblak\NestPay\TransactionClient;
use WP_Error;

use const OBLAK\NPG\BASENAME;
use const OBLAK\NPG\PATH;
use const OBLAK\NPG\VERSION;

use function Oblak\NPG\getCurrencyNumericCode;


class NestPayGateway extends WC_Payment_Gateway {


    /**
     * Whether or not logging is enabled
     *
     * @var bool
     */
    public static $log_enabled = false;

    /**
     * Logger instance
     *
     * @var WC_Logger
     */
    public static $log = false;

    public function __construct() {
        // Predefined Gateway fields
        $this->id                 = 'nestpay';
        $this->has_fields         = true;
        $this->method_title       = __( 'NestPay', 'woocommerce-nestpay' );
        $this->method_description = __( 'NestPay Payment Gateway handles card payments by redirecting users to your bank portal', 'woocommerce-nestpay' );
        $this->supports           = [
            'products',
            'refunds',
        ];

        // Load the settings
        $this->init_form_fields();
        $this->init_settings();

        // User set variables
        $this->title         = $this->get_option( 'title' );
        $this->description   = $this->get_option( 'description' );
        $this->testmode      = 'yes' === $this->get_option( 'testmode', 'no' );
        $this->debug         = 'yes' === $this->get_option( 'debug', 'no' );
        $this->auto_redirect = 'yes' === $this->get_option( 'auto_redirect', 'no' );

        if ( $this->debug ) :
            $this->auto_redirect = false;
        endif;

        self::$log_enabled = $this->debug;

        // Merchant details
        $prefix            = ( $this->testmode ) ? 'test_' : '';
        $this->merchant_id = $this->get_option( "{$prefix}merchant_id" );
        $this->username    = $this->get_option( "{$prefix}username" );
        $this->password    = $this->get_option( "{$prefix}password" );
        $this->payment_url = $this->get_option( "{$prefix}payment_url" );
        $this->api_url     = $this->get_option( "{$prefix}api_url" );
        $this->store_key   = $this->get_option( "{$prefix}store_key" );

        // Store details
        $this->store_currency   = $this->get_option( 'store_currency' );
        $this->store_rsd_fix    = 'yes' === $this->get_option( 'store_rsd_fix', 'no' );
        $this->store_type       = $this->get_option( 'store_type' );
        $this->transaction_type = $this->get_option( 'store_transaction' );

        // Security details
        $this->hcaptcha_key    = $this->get_option( 'hcaptcha_key' );
        $this->hcaptcha_secret = $this->get_option( 'hcaptcha_secret' );

        // Admin actions and filters
        add_action( "woocommerce_update_options_payment_gateways_{$this->id}", [$this, 'process_admin_options'] );

        // Store filters and actions
        add_action( 'woocommerce_checkout_create_order', [$this, 'setNestPayStatus'] );
        add_action( "woocommerce_thankyou_{$this->id}", [$this, 'thankyou_page'] );
        add_action( "woocommerce_receipt_{$this->id}", [$this, 'receipt_page'] );

        // Order filters and actions
        add_action( 'woocommerce_order_status_completed', [$this, 'capture_payment'] );

        new NestPayResponse(
            $this->merchant_id,
            $this->store_key,
            $this->api_url,
            $this->username,
            $this->password
        );

    }

    /**
     * Return whether or not this gateway still requires setup to function.
     *
     * When this gateway is toggled on via AJAX, if this returns true a
     * redirect will occur to the settings page instead.
     *
     * @since 3.4.0
     * @return bool
     */
    public function needs_setup() {
         return empty( $this->merchant_id );
    }

    /**
     * Logging method.
     *
     * @param string $message Log message.
     * @param string $level Optional. Default 'info'. Possible values:
     *                      emergency|alert|critical|error|warning|notice|info|debug.
     */
    public static function log( $message, $level = 'info' ) {

        if ( ! self::$log_enabled ) {
            return;
        }

        if ( empty( self::$log ) ) {
            self::$log = wc_get_logger();
        }

        self::$log->log( $level, $message, ['source' => 'nestpay'] );

    }

    /**
     * Initializes Payment Gateway form fields
     */
    public function init_form_fields() {
        $this->form_fields = include PATH . 'config/settings.php';
    }

    /**
     * Processes and saves options.
     * If there is an error thrown, will continue to save and validate fields, but will leave the erroring field out.
     *
     * @return bool was anything saved?
     */
    public function process_admin_options() {

        $saved = parent::process_admin_options();

        // Maybe clear logs.
        if ( 'yes' !== $this->get_option( 'debug', 'no' ) ) {
            if ( empty( self::$log ) ) {
                self::$log = wc_get_logger();
            }
            self::$log->clear( 'nestpay' );
        }

        return $saved;
    }

    public function process_payment( $order_id ) {

        $order = new WC_Order( $order_id );

        return [
            'result'   => 'success',
            'redirect' => $order->get_checkout_payment_url( true ),
        ];

    }

    public function query_payment( $order_id ) {

        $order = new WC_Order( $order_id );

        $client = ApiClient::getInstance();

        $response = $client->queryPayment( $order->get_id() );

        return $response;

    }

    public function capture_payment( $order_id ) {

        $order = wc_get_order( $order_id );

        if ( $order->get_payment_method() != $this->id || in_array( $order->get_meta( '_nestpay_transaction', true ), ['Auth', 'PostAuth'] ) ) {
            return;
        }

        $client = ApiClient::getInstance();

        $response = $client->capturePayment( $order_id );

        // PostAuth successful
        if ( $response->Response == 'Approved' ) {

            $this->addCaptureMeta( $order, $response );
            $this->addResponseOrderNote( $order, $response, __( 'Capture', 'woocommerce-nestpay' ) );

            return;

        }

        // Post auth maybe not successful, requery
        $response = $client->queryPayment( $order_id );

        if ( $response->CHARGE_TYPE_CD == 'S' ) {

            $this->addCaptureMeta( $order, $response );
            $this->addResponseOrderNote( $order, $response, __( 'Capture', 'woocommerce-nestpay' ) );

            return;

        }

        // Post Auth not successful
        $order->add_order_note( __( 'NestPay Capture not successful', 'woocommerce-nestpay' ) );

    }

    public function void_payment( $order_id ) {

        $order = wc_get_order( $order_id );

        if ( $order->get_payment_method() != $this->id || in_array( $order->get_meta( '_nestpay_transaction', true ), ['Auth', 'PostAuth'] ) ) {
            return;
        }

        $client = ApiClient::getInstance();

        $response = $client->voidPayment( $order_id );

        if ( $response->Response == 'Approved' ) {

            $order->add_meta_data( '_nestpay_transaction', 'Void', true );
            $order->add_meta_data( '_nestpay_void', $response, true );

            $order->save();

            $this->addResponseOrderNote( $order, $response, __( 'Void', 'woocommerce-nestpay' ) );

            return;

        }

    }

    private function addCaptureMeta( WC_Order &$order, ApiResponseDTO &$response ) : void {

        $order->add_meta_data( '_nestpay_transaction', 'PostAuth', true );
        $order->add_meta_data( '_nestpay_postauth', $response, true );

        $order->save();

    }

    private function addResponseOrderNote( WC_Order &$order, ApiResponseDTO &$response, string $action ) : void {

        $order->add_order_note(sprintf(
            '<h4>%s</h4>
            <p>
                <strong>%s</strong>: %s<br>
                <strong>%s</strong>: %s<br>
                <strong>%s</strong>: %s<br>
                <strong>%s</strong>: %s<br>
            </p>',
            __( 'NestPay payment status', 'woocommerce-nestpay' ),
            __( 'Action', 'woocommerce-nestpay' ),
            $action,
            __( 'Transaction date', 'woocommerce-nestpay' ),
            date( 'd. m. Y - H:i', strtotime( $response->TRXDATE ?? $response->AUTH_DTTM ) ),
            __( 'Status code', 'woocommerce-nestpay' ),
            $response->ProcReturnCode,
            __( 'Authorization code', 'woocommerce-nestpay' ),
            $response->AuthCode ?? $response->AUTH_CODE,
        ));

    }

    /**
     * Can the order be refunded by the gateway?
     *
     * NestPay system can only refund captured payments
     *
     * @param  WC_Order $order Order object.
     * @return bool            True if the order can be refunded, false otherwise.
     */
    public function can_refund_order( $order ) : bool {
        return in_array( $order->get_meta( '_nestpay_transaction', true ), ['Auth', 'PostAuth'] );
    }

    public function process_refund( $order_id, $amount = null, $reason = '' ) {

        $order  = wc_get_order( $order_id );
        $client = ApiClient::getInstance();

        if ( is_null( $amount ) ) {
            $amount = $order->get_total();
        }

        if ( ! $this->can_refund_order( $order ) ) {
            return new WP_Error( 'error', __( 'Payment cannot be refunded because it has not been captured.', 'woocommerce-nestpay' ) );
        }

        $response = $client->refundPayment( $order_id, $amount );

        if ( is_null( $response ) ) {
            return new WP_Error(
                'error',
                __( 'An error occurred while refunding the transaction.', 'woocommerce-nestpay' )
            );
        }

        $this->addResponseOrderNote( $order, $response, __( 'Refund', 'woocommerce-nestpay' ) );

        return ( $response->Response == 'Approved' ) ? true : false;

    }

    /**
     *
     * @param  int $order_id Order ID to process payment for
     * @return void
     */
    public function receipt_page( $order_id ) : void {
        echo $this->createGateWayForm( $order_id );
    }

    public function thankyou_page() { }

    /**
     * Generates the NestPay payment gateway form
     *
     * @param integer $order_id
     * @return string
     */
    private function createGateWayForm( int $order_id ) : string {

        $order = wc_get_order( $order_id );

        // Basic shop info needed for form completion
        $shop_home_url = home_url();
        $success_url   = home_url( '/wc-api/' . $order->get_payment_method() );
        $failure_url   = home_url( '/wc-api/' . $order->get_payment_method() );

        // Round the order total to two decimals, and then replace the decimal without thousands separator
        $order_total    = number_format( round( $order->get_total(), 2 ), 2, '.', '' );
        $order_currency = ( $this->store_currency != 0 ) ? $this->store_currency : getCurrencyNumericCode( get_woocommerce_currency() );

        $random_string = bin2hex( random_bytes( 10 ) );
        $trans_hash    = $this->generateTransactionHash(
            $order_id,
            $order_total,
            $random_string,
            $success_url,
            $failure_url,
            $order_currency
        );

        $params = [
            'clientid'      => $this->merchant_id,
            'amount'        => $order_total,
            'okUrl'         => $success_url,
            'failUrl'       => $failure_url,
            'shopurl'       => $shop_home_url,
            'trantype'      => $this->transaction_type,
            'currency'      => $order_currency,
            'rnd'           => $random_string,
            'storetype'     => $this->store_type,
            'hashAlgorithm' => 'ver2',
            'lang'          => 'sr',
            'oid'           => $this->generateOrderID( $order ),
            'encoding'      => 'UTF-8',
            'hash'          => $trans_hash,
        ];

        // Form fields string to populate
        $fields = '';

        foreach ( $params as $name => $value ) {
            $fields .= sprintf(
                '<input type="hidden" name="%s" value="%s">',
                $name,
                $value
            );
        }

        $hcaptcha = ! is_user_logged_in()
            ? sprintf(
                '<input type="hidden" id="hc-active" name="hc_active" value="1">
                <div class="h-captcha" data-sitekey="%s"></div>',
                $this->hcaptcha_key
            )
            : '<input type="hidden" id="hc-active" name="hc_active" value="0">';

        return sprintf(
            '<form method="POST" action="%s" name="pay" id="nestpay-form">
                %s
                %s
                <input class="button button-proceed" type="submit" value="%s" />
            </form>',
            $this->payment_url,
            $fields,
            $hcaptcha,
            __( 'Continue to payment', 'woocommerce-nestpay' )
        );

    }

    private function generateOrderID( WC_Order &$order ) : string {

        return (string) $order->get_id();

    }

    /**
     * Generates the transaction hash using ver2 method
     *
     * Hash for Version 2 is the base64-encoded version of the hashed text which is generated with SHA512 algorithm.
     * For using Hash Version 2, “hashAlgorithm” parameter should be sent in the request with the value of “ver2”
     *
     * @param  string $order_id      Unique order ID.
     * @param  float  $order_total   Money amount to charge to the client.
     * @param  string $random_string Random string to be used as a salt for the hash.
     * @param  string $success_url   URL to return to if the transaction is successful.
     * @param  string $failure_url   URL to return to if the transaction failed.
     * @param  string $currency_code Currency code.
     *
     * @return string              Transaction hash
     */
    private function generateTransactionHash( $order_id, $order_total, $random_string, $success_url, $failure_url, $currency_code ) : string {

        $hash_template = 'merchant_id|order_id|order_total|success_url|failure_url|transaction_type||random_string||||currency_code|store_key';

        $string = strtr($hash_template, [
            'merchant_id'      => $this->merchant_id,
            'order_id'         => $order_id,
            'order_total'      => $order_total,
            'success_url'      => $success_url,
            'failure_url'      => $failure_url,
            'transaction_type' => $this->transaction_type,
            'random_string'    => $random_string,
            'currency_code'    => $currency_code,
            'store_key'        => $this->store_key,
        ]);

        return base64_encode( pack( 'H*', hash( 'sha512', $string ) ) );

    }

    /**
     * Sets the flags for the NestPay response
     *
     * We use those flags to check if the customer was succesfully redirected to okUrl
     *
     * @param  WC_Order $order Order to add the metadata for
     * @return void
     */
    public function setNestPayStatus( WC_Order $order ) : void {

        if ( $order->get_payment_method() != 'nestpay' ) {
            return;
        }

        $order->add_meta_data( '_nestpay_responded', 0, true );
        $order->add_meta_data( '_nestpay_responded_check', 0, true );

        $order->save();

    }


}
