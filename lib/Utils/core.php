<?php
/**
 * Core functions and utilities
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

use Oblak\NPG\WooCommerce_Nestpay;

defined( 'ABSPATH' ) || exit;

/**
 * Returns the main instance of WCNPG
 *
 * @return WooCommerce_Nestpay
 */
function WCNPG() { //phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
    return WooCommerce_Nestpay::get_instance();
}
