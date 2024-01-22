<?php
/**
 * Transaction functions and utilities
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

use Oblak\NPG\WooCommerce\Data\Nestpay_Transaction;

/**
 * Get transaction by transaction ID
 *
 * @param  mixed $id    See `Transaction_Factory::get_transaction` for accepted values.
 * @return Nestpay_Transaction|false Transaction object if found, false otherwise
 */
function nestpay_get_transaction( $id ) {
    if ( ! did_action( 'woocommerce_init' ) ) {
        // Translators: 1: function name 2: woocommerce_init 3: woocommerce_after_register_taxonomy 4: woocommerce_after_register_post_type.
        wc_doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s should not be called before the %2$s, %3$s and %4$s actions have finished.', 'woocommerce' ), 'nestpay_get_transaction', 'woocommerce_init', 'woocommerce_after_register_taxonomy', 'woocommerce_after_register_post_type' ), '3.9' );
        return false;
    }

    return WCNPG()->transaction_factory->get_transaction( $id );
}

/**
 * Get transaction by Transaction ID
 *
 * @param  string $transaction_id Transaction ID from NestPay.
 * @return int                    Transaction ID if found, 0 otherwise
 */
function nestpay_get_transaction_id_by_transid( string $transaction_id ): int {
    return WC_Data_Store::load( 'nestpay-transaction' )->get_transaction_by_transaction_id( $transaction_id )?->ID ?? 0;
}

/**
 * Retrieves transaction IDs for order
 *
 * @param  int $order_id Order ID.
 * @return int[]           Transaction IDs
 */
function nestpay_get_order_transactions( $order_id ) { //phpcs:ignore
    return array();
}

/**
 * Get the list of fields and descriptions for transaction
 * These will be shown to the user on the thank you page and emails
 *
 * @return array
 */
function nestpay_get_transaction_fields() {
    $fields = array(
        'Response'       => __( 'Transaction status', 'wc-serbian-nestpay' ),
        'TransId'        => __( 'Transaction ID', 'wc-serbian-nestpay' ),
        'ProcReturnCode' => __( 'Status code', 'wc-serbian-nestpay' ),
        'AuthCode'       => __( 'Authorization code', 'wc-serbian-nestpay' ),
        'mdStatus'       => __( '3D Status', 'wc-serbian-nestpay' ),
        'MaskedPan'      => __( 'Payment card number', 'wc-serbian-nestpay' ),
        'EXTRA_TRXDATE'  => __( 'Transaction date', 'wc-serbian-nestpay' ),
    );

    /**
     * Filter the list of fields to show on the thank you page and emails
     *
     * @param  array $fields Transaction field key-value pairs.
     * @return array         Transaction field key-value pairs.
     *
     * @since 1.0.0
     */
    return apply_filters( 'woocommerce_nestpay_user_transaction_fields', $fields );
}
