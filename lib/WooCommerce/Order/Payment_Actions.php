<?php
/**
 * Order_Actions class file.
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage WooCommerce\Order
 */

namespace Oblak\NPG\WooCommerce\Order;

use Oblak\NPG\WooCommerce\Gateway\Nestpay_Gateway;
use Oblak\WP\Abstracts\Hook_Runner;
use Oblak\WP\Decorators\Hookable;
use WC_Order;

/**
 * Handles voiding, refunding and capturing payments when order status changes.
 */
#[Hookable( 'woocommerce_init', 99 )]
class Payment_Actions extends Hook_Runner {
    /**
     * Checks if the order is handled by nestpay
     *
     * @param  WC_Order $order Order object.
     * @return boolean         True if the order is handled by nestpay, false otherwise.
     */
    private function is_nestpay_order( $order ) {
        return 'nestpay' === $order->get_payment_method();
    }

    /**
     * Capture funds when the order is completed.
     *
     * @param int      $order_id Order ID.
     * @param WC_Order $order    Order object.
     *
     * @hook woocommerce_order_status_processing_to_completed, woocommerce_order_status_on-hold_to_completed, woocommerce_order_failed_to_completed
     * @type action
     */
    public function capture_payment( $order_id, $order ) {
        if ( ! $this->is_nestpay_order( $order ) || $order->get_meta( '_nestpay_status', true ) === 'charged' ) {
            return;
        }

        /**
         * Gateway type override
         *
         * @var Nestpay_Gateway $gw
         */
        $gw = wc_get_payment_gateway_by_order( $order_id );
        $gw->process_capture( $order_id );
    }

    /**
     * Void the transaction when order is cancelled.
     *
     * @param int      $order_id Order ID.
     * @param WC_Order $order    Order object.
     *
     * @hook woocommerce_order_status_processing_to_cancelled, woocommerce_order_status_on-hold_to_cancelled, woocommerce_order_failed_to_cancelled
     * @type action
     */
    public function void_payment( $order_id, $order ) {
        if ( ! $this->is_nestpay_order( $order ) || $order->get_meta( '_nestpay_status' ) === 'charged' ) {
            return;
        }

        /**
         * Gateway type override
         *
         * @var Nestpay_Gateway $gw
         */
        $gw = wc_get_payment_gateway_by_order( $order_id );
        $gw->process_void( $order_id );
    }
}
