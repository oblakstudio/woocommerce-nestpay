<?php
namespace Oblak\NPG\Email;

use WC_Email;

use const OBLAK\NPG\PATH;

abstract class BaseTransactionEmail extends WC_Email {

    public function __construct() {

        $this->customer_email = true;
        $this->template_base  = PATH . 'woocommerce/';
        $this->template_html  = 'emails/customer-nestpay-status.php';

        parent::__construct();

    }

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
        $this->recipient                      = $this->object->get_billing_email();
        $this->placeholders['{site_title}']   = $this->get_blogname();
        $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
        $this->placeholders['{order_number}'] = $this->object->get_order_number();

        if ( $this->is_enabled() && $this->get_recipient() ) {
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }

        $this->restore_locale();

    }

    public function get_content_html() {
        return wc_get_template_html($this->template_html, [
            'order'              => $this->object,
            'response'           => $this->object->get_meta( '_nestpay_response', true ),
            'email_heading'      => $this->get_heading(),
            'additional_content' => $this->get_additional_content(),
            'plain_text'         => false,
            'email'              => $this,
        ]);

    }

}
