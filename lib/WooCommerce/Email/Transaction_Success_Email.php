<?php
/**
 * Transaction_Success_Email class file.
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage WooCommerce\Email
 */

namespace Oblak\NPG\WooCommerce\Email;

use Oblak\NPG\WooCommerce\Data\Nestpay_Transaction;
use WC_Email;

/**
 * Email sent when transaction is successful
 */
class Transaction_Success_Email extends WC_Email {

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
        $this->id             = 'nestpay_transaction_success';
        $this->title          = __( 'Payment Success', 'woocommerce-nestpay' ) . ' (' . __( 'NestPay', 'woocommerce-nestpay' ) . ')';
        $this->description    = __( 'Payment Success e-mail is sent to the buyer upon succesful payment card transaction', 'woocommerce-nestpay' );
        $this->placeholders   = array (
            '{site_title}'   => $this->get_blogname(),
            '{order_number}' => '',
            '{order_date}'   => '',
        );

        add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array($this, 'trigger'), 10, 2 );
        add_action( 'woocommerce_order_status_pending_to_processing_notification', array($this, 'trigger'), 10, 2 );
        add_action( 'woocommerce_order_status_pending_to_completed_notification', array($this, 'trigger'), 10, 2 );

        add_action( 'woocommerce_order_status_failed_to_processing_notification', array($this, 'trigger'), 10, 2 );
        add_action( 'woocommerce_order_status_failed_to_on-hold_notification', array($this, 'trigger'), 10, 2 );
        add_action( 'woocommerce_order_status_failed_to_completed_notification', array($this, 'trigger'), 10, 2 );

        add_action( 'woocommerce_order_status_cancelled_to_on-hold_notification', array($this, 'trigger'), 10, 2 );
        add_action( 'woocommerce_order_status_cancelled_to_processing_notification', array($this, 'trigger'), 10, 2 );
        add_action( 'woocommerce_order_status_cancelled_to_completed_notification', array($this, 'trigger'), 10, 2 );

        parent::__construct();
    }

    /**
     * Get the default email subject
     *
     * @return string
     */
    public function get_default_subject() {
        return __( 'Your card payment on {site_title} was succesful!', 'woocommerce-nestpay' );
    }

    /**
     * Get the default email heading
     *
     * @return string
     */
    public function get_default_heading() {
        return __( 'Payment confirmation', 'woocommerce-nestpay' );
    }

    /**
     * Trigger the sending of this email.
     *
     * @param int            $order_id The order ID.
     * @param WC_Order|false $order Order object.
     */
    public function trigger( $order_id, $order = false ) {

        if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
            $order = wc_get_order( $order_id );
        }
        if ( ! is_a( $order, 'WC_Order' ) ) {
            return;
        }
        if ( $order->get_payment_method() !== 'nestpay' ) {
            return;
        }

        $this->setup_locale();

        $this->object                         = $order;
        $this->transaction                    = new Nestpay_Transaction( $order->get_meta( '_nestpay_callback_id', true ) );
        $this->recipient                      = $this->object->get_billing_email();
        $this->placeholders['{order_number}'] = $this->object->get_order_number();
        $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );

        if ( $this->is_enabled() && $this->get_recipient() ) {
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }

        $this->restore_locale();

    }

    /**
     * Get the email content in HTML format.
     *
     * @return string
     */
    public function get_content_html() {
        return wc_get_template_html( $this->template_html, array(
            'order'              => $this->object,
            'transaction'        => $this->transaction,
            'email_heading'      => $this->get_heading(),
            'additional_content' => $this->get_additional_content(),
            'sent_to_admin'      => false,
            'plain_text'         => false,
            'email'              => $this,
        ));
    }

}
