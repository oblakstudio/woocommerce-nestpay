<?php
/**
 * Currency functions and utilities
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get supported gateway currencies
 *
 * @return array List of supported currencies.
 * @since 2.0.0
 */
function wcnpg_get_currencies() {
    $raw_currencies = require WCNPG_ABSPATH . 'config/currencies.php';
    $formatted      = array(0 => __( 'WooCommerce currency', 'woocommerce-nestpay' ));

    foreach ( $raw_currencies as $code => $data ) {
        $formatted[ $code ] = $code . ' &ndash; ' . $data['name'];
    }

    return $formatted;
}

/**
 * Returns currency code for given currency symbol
 *
 * @param  string $currency Currency symbol.
 * @return int              Currency code.
 */
function wcnpg_get_currency_code( $currency ) {

    $currencies = wcnpg_get_currencies();

    if ( ! in_array( $currency, array_keys( $currencies ), true ) ) {
        return 941;
    }

    foreach ( $currencies as $code => $name ) {
        if ( strpos( $name, $currency ) !== false ) {
            return $code;
        }
    }
}
