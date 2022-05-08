<?php //phpcs:disable WordPress.Security.NonceVerification.Recommended
/**
 * AdminAssets class file
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
class AdminAssets {
    /** Class Constructor */
    public function __construct() {
        add_filter( 'admin_body_class', array($this, 'add_router_classes'), 9999 );

        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
    }

    public function add_router_classes( $classes ) {
        $page    = sanitize_text_field( wp_unslash( $_GET['page'] ?? '' ) );
        $tab     = sanitize_text_field( wp_unslash( $_GET['tab'] ?? '' ) );
        $section = sanitize_text_field( wp_unslash( $_GET['section'] ?? '' ) );

        if ( 'wc-settings' === $page && 'checkout' === $tab && 'nestpay' === $section ) {
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
