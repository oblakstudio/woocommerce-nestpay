<?php
namespace Oblak\NPG\Admin;

use Automattic\Jetpack\Constants;
use WC_Order;

use const OBLAK\NPG\BASENAME;
use const OBLAK\NPG\VERSION;

class OrderActions
{

    /**
     * Actions to add to the order list
     *
     * @var array
     */
    private static $actions;

    public function __construct()
    {

        self::$actions = [
            'nestpay_query'   => __('Get payment status', 'woocommerce-nestpay'),
            'nestpay_capture' => __('Capture payment', 'woocommerce-nestpay'),
            'nestpay_void'    => __('Void the payment', 'woocommerce-nestpay'),
            // 'nestpay_refund'  => __('Refund the payment', 'woocommerce-nestpay'),
        ];

        add_action('woocommerce_admin_order_actions', [$this, 'addTableOrderActions'], 99, 2);
        add_action('woocommerce_order_item_add_action_buttons', [$this, 'addOrderActions'], 99, 1);

    }

    /**
     * Adds order actions to WooCommerce order table
     *
     * @param  array    $actions Array of existing actions
     * @param  WC_Order $order   Order to add actions for
     * @return array             Modified actions array
     */
    public function addTableOrderActions($actions, $order)
    {

        if ( ($order->get_payment_method() !== 'nestpay') || $order->has_status(['cancelled', 'refunded']) ) :
            return $actions;
        endif;

        $nestpay_meta = $order->get_meta('_nestpay_details', true);
        $transaction  = $order->get_meta('_nestpay_transaction', true);

        $action_url = add_query_arg([
            'action'   => '%s',
            'order_id' => '%s',
            'nonce'    => '%s',
        ], admin_url('admin-ajax.php'));

        foreach(self::$actions as $action => $name) :

            $actions[$action] = [
                'action' => $action,
                'name'   => $name,
                'url'    => sprintf(
                    $action_url,
                    $action,
                    $order->get_id(),
                    wp_create_nonce($action)
                ),
            ];

        endforeach;

        if ( in_array($transaction, ['PostAuth', 'Auth']) ) :
            unset($actions['nestpay_capture']);
            unset($actions['nestpay_void']);
        endif;

        return $actions;

    }

    public function addOrderActions(WC_Order $order) : void {

        $actions = $this->addTableOrderActions([], $order);

        echo '<div class="order-actions">';

        foreach ($actions as $action) {

            printf(
                '<a style="margin-left:10px" class="button button-primary wc-action-button-%s" href="%s">%s</a>',
                $action['action'],
                $action['url'],
                $action['name'],
            );

        }

        echo '</div>';

    }

}
