<?php
/**
 * Admin_Order_Columns class file.
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage WooCommerce\Order
 */

namespace Oblak\NPG\WooCommerce\Order;

use WC_Order;

/**
 * Adds additional information to the order list page.
 */
class Admin_Order_Columns {
    /**
     * Class Constructor
     */
    public function __construct() {
        add_filter( 'manage_shop_order_posts_custom_column', array($this, 'add_nestpay_status_to_order_total'), 99, 1 );
    }

    /**
     * Adds nestpay status to order total column
     *
     * @param string $column Column key.
     */
    public function add_nestpay_status_to_order_total( $column ) {
        /**
         * Order type override
         *
         * @var WC_Order $the_order
         */
        global $the_order;

        if ( 'order_total' !== $column || 'nestpay' !== $the_order->get_payment_method() ) {
            return;
        }

        $nestpay_status = __( 'Unknown', 'woocommerce-nestpay' );

        switch ( $the_order->get_meta( '_nestpay_status', true, 'edit' ) ) {
            case 'reserved':
                $nestpay_status = __( 'Funds reserved', 'woocommerce-nestpay' );
                break;
            case 'charged':
                $nestpay_status = __( 'Funds deposited', 'woocommerce-nestpay' );
                break;
            case 'void':
                $nestpay_status = __( 'Transaction voided', 'woocommerce-nestpay' );
                break;
            case 'refunded':
                $nestpay_status = __( 'Transaction refunded', 'woocommerce-nestpay' );
                break;
        }

        echo '<br>' . esc_html( $nestpay_status );
    }
}
