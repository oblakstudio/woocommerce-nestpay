<?php
/**
 * Transaction_Status_Email class file.
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage WooCommerce\Email
 */

namespace Oblak\NPG\WooCommerce\Email;

use Oblak\NPG\WooCommerce\Data\Nestpay_Transaction;

use WC_Email;
use WC_Order;

/**
 * Email sent after NestPay order payment
 */
class Transaction_Status_Email extends WC_Email {
    /**
     * Transaction object
     *
     * @var Nestpay_Transaction
     */
    protected $transaction;

    /**
     * Class Constructor
     */
    public function __construct() {
        $this->customer_email = true;
        $this->template_base  = WCNPG_PLUGIN_PATH . 'woocommerce/';
        $this->template_html  = 'emails/customer-nestpay-status.php';
        $this->id             = 'nestpay_transaction_status';
        $this->title          = __( 'NestPay Payment Status', 'wc-serbian-nestpay' );
        $this->description    = __( 'Payment Status e-mail is sent to the buyer upon payment card transaction', 'wc-serbian-nestpay' );
        $this->placeholders   = array(
            '{site_title}'     => $this->get_blogname(),
            '{order_number}'   => '',
            '{order_date}'     => '',
            '{payment_status}' => '',
        );

        add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $this, 'trigger' ), 10, 2 );
        add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger' ), 10, 2 );
        add_action( 'woocommerce_order_status_pending_to_completed_notification', array( $this, 'trigger' ), 10, 2 );

        add_action( 'woocommerce_order_status_failed_to_processing_notification', array( $this, 'trigger' ), 10, 2 );
        add_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $this, 'trigger' ), 10, 2 );
        add_action( 'woocommerce_order_status_failed_to_completed_notification', array( $this, 'trigger' ), 10, 2 );

        add_action( 'woocommerce_order_status_cancelled_to_on-hold_notification', array( $this, 'trigger' ), 10, 2 );
        add_action( 'woocommerce_order_status_cancelled_to_processing_notification', array( $this, 'trigger' ), 10, 2 );
        add_action( 'woocommerce_order_status_cancelled_to_completed_notification', array( $this, 'trigger' ), 10, 2 );

        add_action( 'woocommerce_order_status_pending_to_failed_notification', array( $this, 'trigger' ), 10, 2 );

        parent::__construct();
    }

    /**
     * Get the default email subject
     *
     * @return string
     */
    public function get_default_subject() {
        return __( 'Your card payment on {site_title} was {payment_status}!', 'wc-serbian-nestpay' );
    }

    /**
     * Get the default email heading
     *
     * @return string
     */
    public function get_default_heading() {
        return __( 'Payment confirmation', 'wc-serbian-nestpay' );
    }

    /**
     * Trigger the sending of this email.
     *
     * @param int           $order_id The order ID.
     * @param WC_Order|null $order Order object.
     */
    public function trigger( $order_id, $order = null ) {
        $order ??= wc_get_order( $order_id );
        $order   = $order ?: null; // phpcs:ignore Universal.Operators.DisallowShortTernary.Found -- ELVIS LIVES.

        if ( ( ! $order_id || is_null( $order ) ) || 'nestpay' !== $order?->get_payment_method() ) {
            return;
        }

        $this->setup_locale();

        $this->object                           = $order;
        $this->transaction                      = nestpay_get_transaction( $order );
        $this->recipient                        = $this->object->get_billing_email();
        $this->placeholders['{order_number}']   = $this->object->get_order_number();
        $this->placeholders['{order_date}']     = wc_format_datetime( $this->object->get_date_created() );
        $this->placeholders['{payment_status}'] = $this->object->is_paid()
            ? _x( 'successful', 'nestpay payment result', 'wc-serbian-nestpay' )
            : _x( 'unsuccessful', 'nestpay payment result', 'wc-serbian-nestpay' );

        $this->is_enabled() &&
        $this->get_recipient() &&
        $this->send(
            $this->get_recipient(),
            $this->get_subject(),
            $this->get_content(),
            $this->get_headers(),
            $this->get_attachments()
        );

        $this->restore_locale();
    }

    /**
     * Get the email content in HTML format.
     *
     * @return string
     */
    public function get_content_html() {
        return wc_get_template_html(
            $this->template_html,
            array(
				'order'              => $this->object,
				'transaction'        => $this->transaction,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => false,
				'plain_text'         => false,
				'email'              => $this,
            )
        );
    }
}
