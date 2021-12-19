<?php
/**
 * Plugin Name:          WooCommerce NestPay Payment Gateway
 * Plugin URI:           https://oblak.studio/
 * Description:          Payment Gateway for WooCommerce allowing you to process card payments from all banks using NestPay system
 * Version:              1.0.1
 * Author:               Oblak Studio
 * Author URI:           https://oblak.studio
 * Requires PHP:         7.3
 * Requires at least:    5.6
 * WC requires at least: 5.7
 * WC tested up to:      6.0
 * Text Domain:          woocommerce-nestpay
 * Domain Path:          /languages
 *
 * @package WooCommerce NestPay Payment Gateway
 */

defined( 'ABSPATH' ) || exit;

! defined( 'WCNPG_PLUGIN_FILE' ) && define( 'WCNPG_PLUGIN_FILE', __FILE__ );

require __DIR__ . '/vendor/autoload.php';

WCNPG();
