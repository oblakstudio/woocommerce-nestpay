<?php
namespace Oblak\NPG\Admin;

use Oblak\NPG\Email\TransactionFailed;
use WC_Order;
use Oblak\NPG\Email\TransactionStatus;
use Oblak\NPG\Email\TransactionSuccess;

use const OBLAK\NPG\PATH;

class EmailManager {

    private static $templates = [
        'emails/customer-nestpay-status.php',
    ];

    public function __construct() { 

        add_filter('woocommerce_locate_template', [$this, 'addPluginTemplateDir'], 1, 2 );

        add_filter('woocommerce_email_classes', [$this, 'addEmailClasses'], 99 , 1);

        add_filter('woocommerce_order_actions', [$this, 'addEmailActions'], 99 ,1);
        add_action('woocommerce_order_action_send_nestpay_status', [$this, 'sendNestPayConfirmation'], 99, 1);

    }

    public function addPluginTemplateDir(string $template, string $template_file) : string {

        if ( !in_array($template_file, self::$templates) ) {
            return $template;
        }

        return PATH . 'woocommerce/' . $template_file;

    }

    public function addEmailClasses(array $classes) : array {

        $classes['WC_Email_NestPay_Success'] = new TransactionSuccess();
        $classes['WC_Email_NestPay_Failed'] = new TransactionFailed();

        return $classes;

    }

    public function addEmailActions(array $actions) : array {

        /** @var WC_Order $theorder */
        global $theorder;

        if ($theorder->get_payment_method() !== 'nestpay') {
            return $actions;
        }

        $actions['send_nestpay_status'] = __('Send NestPay confirmation', 'woocommerce-nestpay');

        return $actions;
    
    }

    public function sendNestPayConfirmation(WC_Order $order) {

        $emailClass = $order->is_paid() ? 'WC_Email_NestPay_Success' : 'WC_Email_NestPay_Failure';

        /** @var TransactionStatus $mailer */
        $mailer = WC()->mailer()->emails[$emailClass];

        $mailer->trigger($order->get_id(), $order);
        
        add_filter('redirect_post_location', [__CLASS__, 'setEmailSentMessage']);

    }

    public static function setEmailSentMessage( $location ) {
		return add_query_arg( 'message', 11, $location );
	}

}
