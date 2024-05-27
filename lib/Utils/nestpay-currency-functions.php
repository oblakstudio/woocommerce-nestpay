<?php
/**
 * Currency functions and utilities
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

/**
 * Get array of supported NestPay currencies
 *
 * @return array<string, int>
 */
function get_nestpay_supported_currencies(): array {
    return array_intersect_key(
        include WCNPG_ABSPATH . 'config/currencies.php',
        get_woocommerce_currencies(),
    );
}

/**
 * Get NestPay currency code for given currency symbol
 *
 * @param  string $currency_symbol Currency symbol.
 * @return int                     Currency code, or 0 if not found.
 */
function get_nestpay_currency_code( string $currency_symbol ): int {
    return get_nestpay_supported_currencies()[ $currency_symbol ] ?? 0;
}

/**
 * Get supported gateway currencies
 *
 * @return array List of supported currencies.
 * @since 2.0.0
 */
function get_nestpay_currency_options() {
    $woo_currencies = get_woocommerce_currencies();

    $all_currencies = array(
        0 => __( 'WooCommerce currency', 'wc-serbian-nestpay' ),
    );

    foreach ( get_nestpay_supported_currencies() as $currency_code => $id ) {
        $all_currencies[ $id ] = sprintf( '%1$s (%2$s)', $woo_currencies[ $currency_code ], $currency_code );
    }

    return $all_currencies;
}
