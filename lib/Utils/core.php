<?php
/**
 * Main function callback
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

use Oblak\NPG\WooCommerceNestPay;

/**
 * Returns the main instance of WCNPG
 *
 * @return WooCommerceNestPay
 */
function WCNPG() {
    return WooCommerceNestPay::getInstance();
}
