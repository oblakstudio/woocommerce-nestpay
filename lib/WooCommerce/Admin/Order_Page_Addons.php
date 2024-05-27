<?php
/**
 * Order_Page_Addons class file.
 *
 * @package WooCommerce NestPay Payment Gateway
 */

namespace Oblak\NPG\WooCommerce\Admin;

use Oblak\WP\Abstracts\Hook_Runner;
use Oblak\WP\Decorators\Hookable;
use WC_Order;

/**
 * Handles order page addons.
 */
#[Hookable( 'admin_init', 10 )]
class Order_Page_Addons extends Hook_Runner {
    /**
     * Adds nestpay status to order total column
     *
     * @param string   $column Column key.
     * @param WC_Order $order  Order object.
     *
     * @hook     manage_shop_order_posts_custom_column, manage_woocommerce_page_wc-orders_custom_column
     * @type     action
     * @priority 99
     */
	public function display_nestpay_status( string $column, WC_Order|int|null $order = null ) {
        /**
         * Order type override
         *
         * @var WC_Order $the_order
         */
        global $the_order;

        $order ??= $the_order;

        if ( ! $order instanceof WC_Order && is_int( $order ) ) {
            $order = wc_get_order( $order );
        }

		if ( 'order_total' !== $column || 'nestpay' !== $order->get_payment_method() ) {
			return;
		}

		switch ( $order->get_meta( '_nestpay_status', true, 'edit' ) ) {
			case 'reserved':
				$nestpay_status = __( 'Funds reserved', 'wc-serbian-nestpay' );
				break;
			case 'charged':
				$nestpay_status = __( 'Funds deposited', 'wc-serbian-nestpay' );
				break;
			case 'void':
				$nestpay_status = __( 'Transaction voided', 'wc-serbian-nestpay' );
				break;
			case 'refunded':
				$nestpay_status = __( 'Transaction refunded', 'wc-serbian-nestpay' );
				break;
			default:
				$nestpay_status = __( 'Unknown', 'wc-serbian-nestpay' );
				break;
		}

		echo '<br>' . esc_html( $nestpay_status );
	}
}
