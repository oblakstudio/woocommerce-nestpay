<?php //phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
/**
 * Nestpay_Transaction_Data_Store class file
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage Data
 */

namespace Oblak\NPG\WooCommerce\Data;

use WC_Data_Store_WP;
use WC_Object_Data_Store_Interface;

/**
 * Nestpay_Transaction_Data_Store class
 */
class Nestpay_Transaction_Data_Store extends WC_Data_Store_WP implements WC_Object_Data_Store_Interface {

    /**
     * Columns for the transactions table.
     *
     * @var array
     */
    private $columns = array(
        'order_id',
        'oid',
        'ReturnOid',
        'PAResSyntaxOK',
        'refreshtime',
        'lang',
        'merchantID',
        'amount',
        'sID',
        'ACQBIN',
        'Ecom_Payment_Card_ExpDate_Year',
        'Ecom_Payment_Card_ExpDate_Month',
        'isHPPCall',
        'EXTRA_CARDBRAND',
        'MaskedPan',
        'acqStan',
        'EXTRA_UCAFINDICATOR',
        'clientIp',
        'trantype',
        'protocol',
        'iReqDetail',
        'md',
        'ProcReturnCode',
        'instalment',
        'payResults_dsId',
        'vendorCode',
        'TransId',
        'EXTRA_TRXDATE',
        'signature',
        'storetype',
        'iReqCode',
        'veresEnrolledStatus',
        'Response',
        'SettleId',
        'orgHash',
        'HASH',
        'HASHPARAMS',
        'HASHPARAMSVAL',
        'mdErrorMsg',
        'ErrMsg',
        'PAResVerified',
        'shopurl',
        'cavv',
        'digest',
        'HostRefNum',
        'callbackCall',
        'AuthCode',
        'cavvAlgorithm',
        'xid',
        'encoding',
        'currency',
        'mdStatus',
        'dsId',
        'eci',
        'version',
        'orgRnd',
        'EXTRA_CARDISSUER',
        'clientid',
        'txstatus',
        'rnd',
        'hc_active',
    );

    /**
     * Creates a new transaction in the database.
     *
     * @param Nestpay_Transaction $transaction Transaction object.
     */
    public function create( &$transaction ) {
        global $wpdb;

        $transaction_to_insert = array();

        foreach ( $this->columns as $prop ) {
            $transaction_to_insert[ $prop ] = $transaction->{"get_$prop"}( 'edit' );
        }

        $wpdb->insert(
            $wpdb->npp_transactions,
            $transaction_to_insert,
        );

        $transaction_id = $wpdb->insert_id;
        $transaction->set_id( $transaction_id );
    }

    /**
     * Reads a transaction from the database.
     *
     * @param Nestpay_Transaction $transaction Transaction object.
     */
    public function read( &$transaction ) {
        global $wpdb;

        $transaction->set_defaults();
        $transaction_row = false;

        $transaction_id = $transaction->get_id();

        if ( 0 !== $transaction_id ) {
            $transaction_row = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->npp_transactions} WHERE id = %d",
                    $transaction_id
                ),
                ARRAY_A
            );
        }

        if ( $transaction_row ) {
            $transaction->set_props( $transaction_row, 'set' );
            $transaction->set_object_read();

            return;
        }

        $transaction->set_object_read( true );

    }

    /**
     * Updates a transaction in the database.
     *
     * @param Nestpay_Transaction $transaction Transaction object.
     */
    public function update( &$transaction ) {

    }

    /**
     * Deletes a transaction from the database.
     *
     * @param Nestpay_Transaction $transaction Transaction object.
     * @param array               $args        Unused.
     */
    public function delete( &$transaction, $args = array() ) {

    }

    /**
     * Retrieve transaction by transaction ID.
     *
     * @param string $transaction_id Transaction id.
     * @return object|null           Transaction row if found, null otherwise.
     */
    public function get_transaction_by_transaction_id( $transaction_id ) {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->npp_transactions} WHERE TransId = %s",
                $transaction_id
            )
        );
    }

}
