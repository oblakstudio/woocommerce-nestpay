<?php
/**
 * NestPay_Transaction_Factory class file
 *
 * @package WooCommerce NestPay Payment Gateway
 */

namespace Oblak\NPG\WooCommerce\Data;

use Oblak\WP\Decorators\Hookable;
use WC_Order;

/**
 * Transaction factory
 */
#[Hookable( 'woocommerce_init', '10' )]
class NestPay_Transaction_Factory {
    /**
     * Constructor
     */
    public function __construct() {
        WCNPG()->transaction_factory = &$this;
    }

    /**
     * Get a transaction
     *
     * @param  object|int|bool|Nestpay_Transaction|WC_Order $transaction_id Transaction row, Transaction instance, Transaction ID, Order instance.
     * @return Nestpay_Transaction|bool False if transaction not found
     */
    public function get_transaction( $transaction_id = false ) {
        $transaction_id = $this->get_transaction_id( $transaction_id );

        if ( ! $transaction_id ) {
            return false;
        }

        $trans_type = $this->get_transaction_type( $transaction_id );
        $classname  = $this->get_transaction_classname( $transaction_id, $trans_type );

        try {
            return new $classname( $transaction_id );
        } catch ( \Exception $e ) {
            return false;
        }
    }

    /**
     * Gets a Transaction classname and allows filtering. Returns Nestpay_Transaction if the class does not exist.
     *
     * @param  int    $transaction_id Transaction ID.
     * @param  string $request_type   Request type.
     * @return string                 Transaction classname
     */
    public static function get_transaction_classname( int $transaction_id, string $request_type ): string { //phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter, WordPress.Methods.FunctionOpeningBrace.SpacingAfterOpen
        $classname = apply_filters( 'woocommerce_nestpay_transaction_class', Nestpay_Transaction::class, $transaction_id ); //phpcs:ignore WooCommerce.Commenting

        if ( ! $classname || ! class_exists( $classname ) ) {
            $classname = Nestpay_Transaction::class;
        }

        return $classname;
    }

    /**
     * Get the transaction type for a transaction
     *
     * @param  int $transaction_id Transaction ID.
     * @return string              Transaction type
     */
    public static function get_transaction_type( int $transaction_id ) {
        // Allows the override of the transaction type lookup.
        $transaction_type = apply_filters( 'woocommerce_nestpay_transaction_type_query', false, $transaction_id ); //phpcs:ignore WooCommerce.Commenting
        $transaction_type = $transaction_type ?: \WC_Data_Store::load( 'nestpay-transaction' )->get_transaction_type( $transaction_id ); // phpcs:ignore Universal.Operators.DisallowShortTernary.Found

        return $transaction_type;
    }

    /**
     * Get transaction ID depending on what was passed
     *
     * @param  object|int|string|bool|Nestpay_Transaction|WC_Order $transaction Transaction row, Transaction instance, Transaction ID, Order instance.
     * @return int|bool Transaction ID if found, false otherwise
     */
    private function get_transaction_id( $transaction ) {
        if ( is_numeric( $transaction ) ) {
            return $transaction;
        } elseif ( $transaction instanceof Nestpay_Transaction ) {
            return $transaction->get_id();
        } elseif ( ! empty( $transaction->ID ) ) {
            return $transaction->ID;
        } elseif ( $transaction instanceof WC_Order ) {
            $id = $transaction->get_meta( '_nestpay_callback_id', true );

            return ! empty( $id ) ? $id : false;
		} else {
			return false;
		}
	}
}
