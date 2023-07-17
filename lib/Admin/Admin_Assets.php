<?php //phpcs:disable WordPress.Security.NonceVerification.Recommended
/**
 * Admin_Assets class file
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage Admin
 */

namespace Oblak\NPG\Admin;

use Automattic\Jetpack\Constants;

/**
 * Admin Assets class
 *
 * @since 2.0.0
 */
class Admin_Assets {
    /** Class Constructor */
    public function __construct() {
        add_filter( 'admin_body_class', array($this, 'add_router_classes'), 9999 );

        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
    }

    /**
     * Add needed classes for WPRouter
     *
     * @param  string $classes Current classes.
     * @return string          Updated classes.
     */
    public function add_router_classes( $classes ) {
        $get_array = wc_clean( wp_unslash( $_GET ) );

        if ( 'wc-settings' === $get_array['page'] ?? '' && 'checkout' === $get_array['tab'] ?? '' && 'nestpay' === $get_array['section'] ?? '' ) {
            $classes .= ' nestpay-settings ';
        }

        return $classes;
    }

    /**
     * Enqueue Admin scripts
     */
    public function admin_scripts() {
        $suffix = Constants::is_true( 'SCRIPT_DEBUG' ) ? '' : '.min';

        wp_register_script( 'woocommerce_nestpay_admin', WCNPG()->plugin_url() . "/dist/scripts/admin{$suffix}.js", array(), WCNPG()->version, true );

        wp_enqueue_script( 'woocommerce_nestpay_admin' );
    }
}
