<?php //phpcs:disable WordPress.Security.NonceVerification.Recommended
/**
 * Admin_Assets class file
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage Admin
 */

namespace Oblak\NPG\WooCommerce\Admin;

use Oblak\WP\Abstracts\Hook_Runner;
use Oblak\WP\Decorators\Hookable;

/**
 * Admin Assets class
 *
 * @since 2.0.0
 */
#[Hookable( 'admin_init', 10 )]
class Admin_Assets extends Hook_Runner {
    /**
     * Add needed classes for WPRouter
     *
     * @param  string $classes Current classes.
     * @return string          Updated classes.
     *
     * @hook     admin_body_class
     * @type     filter
     * @priority 9999
     */
    public function add_router_classes( $classes ) {
        $get_array = wc_clean( wp_unslash( $_GET ) );

        if (
            'wc-settings' === ( $get_array['page'] ?? '' ) &&
            'checkout' === ( $get_array['tab'] ?? '' ) &&
            'nestpay' === ( $get_array['section'] ?? '' )
        ) {
            $classes .= ' nestpay-settings ';
        }

        return $classes;
    }

    /**
     * Sets the proper image select URL
     *
     * @param  string $image_url  Image URL.
     * @param  string $option_key Option key.
     *
     * @hook woocommerce_image_select_option_image_url
     * @type filter
     */
    public function set_image_option_url( string $image_url, string $option_key ): string {
        if ( 'woocommerce_nestpay_bank' !== $option_key ) {
            return $image_url;
        }

        return WCNPG()->asset_uri( $image_url );
    }
}
