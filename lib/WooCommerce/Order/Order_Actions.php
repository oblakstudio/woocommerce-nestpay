<?php
/**
 * Order_Actions class file.
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage WooCommerce\Order
 */

namespace Oblak\NPG\WooCommerce\Order;

use Oblak\NPG\WooCommerce\Gateway\Nestpay_Gateway;
use WC_Order;

/**
 * Handles voiding, refunding and capturing payments when order status changes.
 */
class Order_Actions {

    /**
     * Class constructor.
     */
    public function __construct() {
        add_action( 'woocommerce_order_status_processing_to_completed', array( $this, 'capture_payment' ), 50, 2 );
        add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'capture_payment' ), 50, 2 );
        add_action( 'woocommerce_order_failed_to_completed', array( $this, 'capture_payment' ), 50, 2 );

        add_action( 'woocommerce_order_status_processing_to_cancelled', array( $this, 'void_payment' ), 50, 2 );
        add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( $this, 'void_payment' ), 50, 2 );
        add_action( 'woocommerce_order_failed_to_cancelled', array( $this, 'void_payment' ), 50, 2 );

        add_action( 'woocommerce_order_status_completed_to_cancelled', array( $this, 'refund_payment' ), 50, 2 );
    }

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
