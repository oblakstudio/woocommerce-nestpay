<?php //phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag
/**
 * NestPayGateway class file
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage Gateway
 */

namespace Oblak\NPG\WooCommerce\Gateway;

use Oblak\NPG\WooCommerce\Data\Nestpay_Transaction;
use WC_Logger;
use WC_Order;
use WC_Payment_Gateway;

use WP_Error;

/**
 * Main NestPay payment gateway class
 *
 * @since 1.0.0
 */
class Nestpay_Gateway extends WC_Payment_Gateway {
    /**
     * Is test mode active?
     *
     * @var bool
     */
    private $testmode;

    /**
     * Is debug mode active?
     *
     * @var bool
     */
    private $debug;

    /**
     * Is auto redirect enabled?
     *
     * @var bool
     */
    private $auto_redirect;

    /**
     * Merchant ID
     *
     * @var string
     */
    private $merchant_id;

    /**
     * Username
     *
     * @var string
     */
    private $username;

    /**
     * Password
     *
     * @var string
     */
    private $password;

    /**
     * Payment URL
     *
     * @var string
     */
    private $payment_url;

    /**
     * API URL
     *
     * @var string
     */
    private $api_url;

    /**
     * Store key
     *
     * @var string
     */
    private $store_key;

    /**
     * Store currency
     *
     * @var string
     */
    private $store_currency;

    /**
     * Transaction type
     *
     * @var string
     */
    private $transaction_type;

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

    /**
     * Class constructor
     */
    public function __construct() {
        // Predefined Gateway fields.
        $this->id                 = 'nestpay';
        $this->icon               = apply_filters( 'woocommerce_nestpay_payment_icon', '' ); //phpcs:ignore
        $this->has_fields         = false;
        $this->method_title       = __( 'NestPay', 'wc-serbian-nestpay' );
        $this->method_description = __( 'NestPay Payment Gateway handles card payments by redirecting users to the bank portal', 'wc-serbian-nestpay' );
        $this->supports           = array(
            'products',
            'refunds',
        );

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // User set variables.
        $this->title         = $this->get_option( 'title' );
        $this->description   = $this->get_option( 'description' );
        $this->testmode      = 'yes' === $this->get_option( 'testmode', 'no' );
        $this->debug         = 'yes' === $this->get_option( 'debug', 'no' );
        $this->auto_redirect = 'yes' === $this->get_option( 'auto_redirect', 'no' );

        if ( $this->debug ) {
            $this->auto_redirect = false;
        }

        self::$log_enabled = $this->debug;

        // Merchant details.
        $mode     = $this->testmode ? 'test' : 'production';
        $fields   = array( 'merchant_id', 'username', 'password', 'store_key' );
        $prefix   = 'test' === $mode ? 'test_' : '';
        $api_urls = nestpay_get_endpoints( $this->get_option( 'bank', 'intesa-rs' ), $mode );

        foreach ( $fields as $field ) {
            $this->{$field} = $this->get_option( "{$prefix}{$field}" );
        }

        $this->payment_url = $api_urls['bib'];
        $this->api_url     = $api_urls['api'];

        // Store details.
        $this->store_currency   = 0 === intval( $this->get_option( 'store_currency', 0 ) ) ? get_nestpay_currency_code( get_woocommerce_currency() ) : (int) $this->get_option( 'store_currency' );
        $this->transaction_type = $this->get_option( 'store_transaction' );

        // Admin actions and filters.
        add_action( "woocommerce_update_options_payment_gateways_{$this->id}", array( $this, 'process_admin_options' ) );

        // Store filters and actions.
        add_action( "woocommerce_receipt_{$this->id}", array( $this, 'receipt_page' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_hcaptcha' ), 999 );

        // NestPay response handler.
        new Nestpay_Response(
            $this->merchant_id,
            $this->store_key,
        );

        WCNPG()->client = new Nestpay_Client(
            $this->merchant_id,
            $this->username,
            $this->password,
            $this->api_url,
        );
    }

    /**
     * Logging method.
     *
     * @param string $message Log message.
     * @param string $level Optional. Default 'info'. Possible values:
     *                      emergency | alert | critical | error | warning | notice | info | debug.
     */
    public static function log( $message, $level = 'info' ) {
        if ( ! self::$log_enabled ) {
            return;
        }

        if ( empty( self::$log ) ) {
            self::$log = wc_get_logger();
        }

        self::$log->log( $level, $message, array( 'source' => 'nestpay' ) );
    }

    /**
     * {@inheritDoc}
     */
    public function needs_setup() {
        return ! $this->merchant_id || ! $this->username || ! $this->password || ! $this->payment_url;
    }

    /**
     * {@inheritdoc}
     */
    public function is_available() {
        return parent::is_available() && in_array( $this->store_currency, get_nestpay_supported_currencies(), true );
    }
    /**
     * {@inheritDoc}
     */
    public function init_form_fields() {
        $this->form_fields = include WCNPG_ABSPATH . 'config/settings.php';
    }

    /**
     * {@inheritDoc}
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

    /**
     * {@inheritDoc}
     */
    public function process_payment( $order_id ) {
        $order = new WC_Order( $order_id );

        return array(
            'result'   => 'success',
            'redirect' => $order->get_checkout_payment_url( true ),
        );
    }

    /**
     * Retrieves the transaction status from NestPay.
     *
     * @param  int $order_id       Order ID.
     * @return Nestpay_Transaction The transaction object.
     */
    public function query_payment( $order_id ) {
        $order = wc_get_order( $order_id );

        return WCNPG()->client->query_payment( $order );
    }

    /**
     * Processes capture for the order.
     *
     * @param int $order_id Order ID.
     */
    public function process_capture( $order_id ) {
        $order = wc_get_order( $order_id );

        $transaction = WCNPG()->client->capture_payment( $order );

        if ( $transaction->get_Response() === 'Approved' ) {
            $order->add_meta_data( '_nestpay_status', 'charged', true );
        }

        $order->add_order_note( Nestpay_Response::generate_order_note( $transaction ) );
        $order->save();
    }

    /**
     * Process a transaction void for the order
     *
     * @param int $order_id Order ID.
     */
    public function process_void( $order_id ) {
        $order = wc_get_order( $order_id );

        $transaction = WCNPG()->client->void_payment( $order );

        if ( $transaction->get_Response() === 'Approved' ) {
            $order->add_meta_data( '_nestpay_status', 'voided', true );
        }

        $order->add_order_note( Nestpay_Response::generate_order_note( $transaction ) );
        $order->save();
    }

    /**
     * {@inheritDoc}
     */
    public function can_refund_order( $order ): bool {
        return ! in_array( $order->get_meta( '_nestpay_status', true ), array( 'refunded', 'voided' ), true );
    }

    /**
     * {@inheritDoc}
     */
    public function process_refund( $order_id, $amount = null, $reason = '' ) {
        $order = wc_get_order( $order_id );

        if ( is_null( $amount ) || 0 === $amount ) {
            return false;
        }

        if ( ! $this->can_refund_order( $order ) ) {
            return new WP_Error( 'error', __( 'Payment cannot be refunded.', 'wc-serbian-nestpay' ) );
        }

        $future_status = $order->get_meta( '_nestpay_status', true ) === 'charged' ? 'refunded' : 'voided';

        $transaction = 'refunded' === $future_status
            ? WCNPG()->client->refund_payment( $order, $amount )
            : WCNPG()->client->void_payment( $order );

        $refund_ok = $transaction->get_Response() === 'Approved';

        if ( ! $refund_ok ) {
            return false;
        }

        $order->add_meta_data( '_nestpay_status', $future_status, true );
        $order->add_order_note( Nestpay_Response::generate_order_note( $transaction ) );

        $order->save();

        return true;
    }

    /**
     * Show the gateway form on the checkout page.
     *
     * @param  int $order_id Order ID to process payment for.
     */
    public function receipt_page( $order_id ) {
        $order = wc_get_order( $order_id );

        // Basic shop info needed for form completion.
        $shop_home_url    = home_url();
        $success_url      = home_url( '/wc-api/' . $order->get_payment_method() );
        $failure_url      = home_url( '/wc-api/' . $order->get_payment_method() );
        $transaction_type = $this->transaction_type;

        if ( 'Automatic' === $transaction_type ) {
            $transaction_type = $order->needs_processing() ? 'PreAuth' : 'Auth';
        }

        // Round the order total to two decimals, and then replace the decimal without thousands separator.
        $order_total    = number_format( round( $order->get_total(), 2 ), 2, '.', '' );
        $order_currency = $this->store_currency;

        $random_string = wp_generate_password( 18, false );
        $trans_hash    = $this->generate_transaction_hash( $order->get_order_number(), $order_total, $random_string, $success_url, $failure_url, $transaction_type, $order_currency );

        $customer_is_company = 'company' === $order->get_meta( '_billing_type', true ) || '' !== $order->get_billing_company();

        $params = array(
            // Default params needed to process payment.
            'clientid'         => $this->merchant_id,
            'amount'           => $order_total,
            'okUrl'            => $success_url,
            'failUrl'          => $failure_url,
            'shopurl'          => $shop_home_url,
            'trantype'         => $transaction_type,
            'currency'         => $order_currency,
            'rnd'              => $random_string,
            'storetype'        => '3d_pay_hosting',
            'hashAlgorithm'    => 'ver2',
            'lang'             => 'sr',
            'oid'              => (string) $order->get_order_number(),
            'encoding'         => 'UTF-8',
            'hash'             => $trans_hash,
            // Customer details.
            'printbillTo'      => 1,
            'tel'              => $order->get_billing_phone(),
            'email'            => $order->get_billing_email(),
            'BillToCompany'    => $customer_is_company ? $order->get_billing_company() : '',
            'BillToName'       => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'BillToStreet1'    => $order->get_billing_address_1(),
            'BillToCity'       => $order->get_billing_city(),
            'BillToPostalCode' => $order->get_billing_postcode(),
            'BillToCountry'    => $order->get_billing_country(),
        );

        wc_get_template(
            'checkout/form-nestpay.php',
            array(
				'payment_url'        => $this->payment_url,
				'auto_redirect'      => $this->auto_redirect,
				'transaction_fields' => $params,
				'enable_hcaptcha'    => ! is_user_logged_in(),
            )
        );
    }

    /**
     * Generates the transaction hash using ver2 method
     *
     * Hash for Version 2 is the base64-encoded version of the hashed text which is generated with SHA512 algorithm.
     * For using Hash Version 2, “hashAlgorithm” parameter should be sent in the request with the value of “ver2”
     *
     * @param  string $order_id         Unique order ID.
     * @param  float  $order_total      Money amount to charge to the client.
     * @param  string $random_string    Random string to be used as a salt for the hash.
     * @param  string $success_url      URL to return to if the transaction is successful.
     * @param  string $failure_url      URL to return to if the transaction failed.
     * @param  string $transaction_type Transaction type.
     * @param  string $currency_code    Currency code.
     * @return string                   Transaction hash
     */
    private function generate_transaction_hash( $order_id, $order_total, $random_string, $success_url, $failure_url, $transaction_type, $currency_code ): string {
        $hash_template = 'merchant_id|order_id|order_total|success_url|failure_url|transaction_type||random_string||||currency_code|store_key';

        $string = strtr(
            $hash_template,
            array(
				'merchant_id'      => $this->merchant_id,
				'order_id'         => $order_id,
				'order_total'      => $order_total,
				'success_url'      => $success_url,
				'failure_url'      => $failure_url,
				'transaction_type' => $transaction_type,
				'random_string'    => $random_string,
				'currency_code'    => $currency_code,
				'store_key'        => $this->store_key,
            )
        );

        //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
        return base64_encode( pack( 'H*', hash( 'sha512', $string ) ) );
    }

    /**
     * Enqueues hCaptcha JS file
     */
    public function enqueue_hcaptcha() {
        if ( ! is_checkout() && ! is_wc_endpoint_url( 'order-pay' ) ) {
            return;
        }

        wp_register_script( 'woocommerce-nestpay-hcaptcha', 'https://hcaptcha.com/1/api.js', array(), WCNPG()->version, true );

        wp_enqueue_script( 'woocommerce-nestpay-hcaptcha' );
    }
}
