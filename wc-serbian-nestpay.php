<?php
/**
 * Plugin Name:          Payment gateway NestPay for WooCommerce
 * Plugin URI:           https://oblak.studio/
 * Description:          Payment Gateway for WooCommerce allowing you to process card payments from all banks using NestPay system
 * Version:              1.2.2
 * Author:               Oblak Studio
 * Author URI:           https://oblak.studio
 * Requires at least:    6.2
 * Requires PHP:         8.0
 * WC requires at least: 8.0
 * WC tested up to:      8.4
 * Text Domain:          wc-serbian-nestpay
 * Domain Path:          /languages
 *
 * @package WooCommerce NestPay Payment Gateway
 */

defined( 'ABSPATH' ) || exit;

! defined( 'WCNPG_PLUGIN_FILE' ) && define( 'WCNPG_PLUGIN_FILE', __FILE__ );
! defined( 'WCNPG_ABSPATH' ) && define( 'WCNPG_ABSPATH', dirname( WCNPG_PLUGIN_FILE ) . '/' );
! defined( 'WCNPG_PLUGIN_BASENAME' ) && define( 'WCNPG_PLUGIN_BASENAME', plugin_basename( WCNPG_PLUGIN_FILE ) );
! defined( 'WCNPG_PLUGIN_PATH' ) && define( 'WCNPG_PLUGIN_PATH', plugin_dir_path( WCNPG_PLUGIN_FILE ) );
! defined( 'WCNPG_VERSION' ) && define( 'WCNPG_VERSION', '1.2.1' );

require __DIR__ . '/vendor/autoload_packages.php';

add_action( 'woocommerce_loaded', 'WCNPG', 0 );
