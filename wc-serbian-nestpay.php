<?php
/**
 * Plugin Name:          Payment gateway NestPay for WooCommerce
 * Plugin URI:           https://oblak.studio/
 * Description:          Payment Gateway for WooCommerce allowing you to process card payments from all banks using NestPay system
 * Version:              1.2.2
 * Author:               Oblak Studio
 * Author URI:           https://oblak.studio
 * Requires PHP:         7.3
 * Requires at least:    6.0
 * WC requires at least: 7.5
 * WC tested up to:      8.2
 * Text Domain:          wc-serbian-nestpay
 * Domain Path:          /languages
 *
 * @package WooCommerce NestPay Payment Gateway
 */

defined( 'ABSPATH' ) || exit;

! defined( 'WCNPG_PLUGIN_FILE' ) && define( 'WCNPG_PLUGIN_FILE', __FILE__ );

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/lib/Utils/core.php';
require __DIR__ . '/lib/Utils/transaction.php';
require __DIR__ . '/lib/Utils/currency.php';


add_action( 'woocommerce_loaded', 'WCNPG' );
