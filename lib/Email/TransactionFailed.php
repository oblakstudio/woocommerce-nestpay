<?php
namespace Oblak\NPG\Email;

class TransactionFailed extends BaseTransactionEmail {

    public function __construct() {

        $this->id             = 'nestpay_transaction_success';
        $this->title          = __('Transaction Failed', 'woocommerce-nestpay') . ' (' . __('NestPay', 'woocommerce-nestpay') . ')';
        $this->description    = __('Transaction failure e-mail is sent to the buyer upon unsuccesful payment card transaction', 'woocommerce-nestpay');

        parent::__construct();

    }

    public function get_default_subject() {
        return __( 'Your card payment on {site_title} was not succesful!', 'woocommerce-nestpay' );
    }

    public function get_default_heading() {
        return __( 'Transaction details', 'woocommerce-nestpay' );
    }

}
