<?php
namespace Oblak\NPG\Email;

class TransactionSuccess extends BaseTransactionEmail {

    public function __construct() {

        $this->id             = 'nestpay_transaction_success';
        $this->title          = __('Transaction Success', 'woocommerce-nestpay') . ' (' . __('NestPay', 'woocommerce-nestpay') . ')';
        $this->description    = __('Transaction Success e-mail is sent to the buyer upon succesful payment card transaction', 'woocommerce-nestpay');

        parent::__construct();

    }

    public function get_default_subject() {
        return __( 'Your card payment on {site_title} was succesful!', 'woocommerce-nestpay' );
    }

    public function get_default_heading() {
        return __( 'Payment confirmation', 'woocommerce-nestpay' );
    }

}
