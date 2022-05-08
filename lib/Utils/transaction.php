<?php
/**
 * Transaction functions and utilities
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

use Oblak\NPG\WooCommerce\Data\Nestpay_Transaction_Data_Store;
use Oblak\NPG\WooCommerce\Data\Nestpay_Transaction;

defined( 'ABSPATH' ) || exit;

/**
 * Get transaction by transaction ID
 *
 * @param  string $transaction_id Transaction ID from NestPay.
 * @return Nestpay_Transaction|false   Transaction object if found, false otherwise
 */
function wcnpg_get_transaction_by_id( $transaction_id ) {
    /**
     * Transaction DataStore
     *
     * @var Nestpay_Transaction_Data_Store $data_store Data store object.
     */
    $data_store = WC_Data_Store::load( 'nestpay-transaction' );

    $transaction_row = $data_store->get_transaction_by_transaction_id( $transaction_id );

    return ! is_null( $transaction_row ) ? new Nestpay_Transaction( $transaction_row ) : false;
}

/**
 * Retrieves transaction IDs for order
 *
 * @param  int $order_id Order ID.
 * @return int[]           Transaction IDs
 */
function wcnpg_get_transactions_for_order( $order_id ) {
    return array();
}

/**
 * Get the list of fields and descriptions for transaction
 * These will be shown to the user on the thank you page and emails
 *
 * @return array
 */
function wcnpg_get_user_transaction_fields() {
    $fields = array(
        'Response'       => __( 'Transaction status', 'woocommerce-nestpay' ),
        'TransId'        => __( 'Transaction ID', 'woocommerce-nestpay' ),
        'ProcReturnCode' => __( 'Status code', 'woocommerce-nestpay' ),
        'AuthCode'       => __( 'Authorization code', 'woocommerce-nestpay' ),
        'mdStatus'       => __( '3D Status', 'woocommerce-nestpay' ),
        'MaskedPan'      => __( 'Payment card number', 'woocommerce-nestpay' ),
    );

    /**
     * Filter the list of fields to show on the thank you page and emails
     *
     * @since 1.0.0
     * @param array    $fields Transaction field key-value pairs
     */
    return apply_filters( 'woocommerce_nestpay_user_transaction_fields', $fields );

}
