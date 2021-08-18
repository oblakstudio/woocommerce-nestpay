<?php
/**
 * Plugin Name:          WooCommerce NestPay Payment Gateway
 * Plugin URI:           https://oblak.studio/
 * Description:          Payment Gateway for WooCommerce allowing you to process card payments from all banks using NestPay system
 * Version:              1.0.0
 * Author:               Oblak Studio
 * Author URI:           https://oblak.studio
 * Requires PHP:         7.4
 * Requires at least:    5.5
 * WC requires at least: 5.0
 * WC tested up to:      5.6
 * Text Domain:          woocommerce-nestpay
 * Domain Path:          /languages
*/

namespace Oblak\NPG;

use Exception;
use Oblak\NPG\Bootstrap as NPG;

// Prevent direct access
!defined('WPINC') && die;
// Define Main plugin file
!defined('OBLAK\NPG\FILE') && define('OBLAK\NPG\FILE', __FILE__);
//Define Basename
!defined('OBLAK\NPG\BASENAME') && define('OBLAK\NPG\BASENAME', plugin_basename(FILE));
//Define internal path
!defined('OBLAK\NPG\PATH') && define('OBLAK\NPG\PATH', plugin_dir_path( FILE ));
// Define internal version
!defined('OBLAK\NPG\VERSION') && define ('OBLAK\NPG\VERSION', '1.0.0');

// Bootstrap the plugin
require (PATH . '/vendor/autoload.php');

function runNPG() : NPG {

    global $wp_version;

    if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
        throw new Exception( __('NestPay for WooCommerce requires PHP 7.4 or greater', 'woocommerce-nestpay') );
    }

    if ( version_compare($wp_version, '5.5', '<') ) {
        throw new Exception( __('NestPay for WooCommerce requires WP 5.5 or greater', 'woocommerce-nestpay') );
    }

    return NPG::instance();

}

try {

    runNPG();

} catch (Exception $e) {

    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    deactivate_plugins( __FILE__ );
    wp_die($e->getMessage());

}

