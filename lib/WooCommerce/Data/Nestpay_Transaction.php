<?php
/**
 * Nestpay_Transaction class file
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage Data
 */

namespace Oblak\NPG\WooCommerce\Data;

use WC_Data;
use WC_Data_Store;

/**
 * NestPay Transaction class.
 *
 * Represents one row in the transaction table.
 *
 * @since 2.0.0
 * @uses WC_Data
 *
 * @todo Add short descriptions for setters
 */
class Nestpay_Transaction extends WC_Data {

    /**
     * Object type
     *
     * @var string
     */
    protected $object_type = 'nestpay-transaction';

    /**
     * Cache group
     *
     * @var string
     */
    protected $cache_group = 'nestpay-transaction';

    /**
     * Unique Object ID
     *
     * @var integer
     */
    protected $id = 0;

    /**
     * Object Props
     *
     * @var array
     */
    protected $data = array(
        'order_id'                        => '',
        'oid'                             => '',
        'ReturnOid'                       => '',
        'PAResSyntaxOK'                   => '',
        'refreshtime'                     => '',
        'lang'                            => '',
        'merchantID'                      => '',
        'amount'                          => '',
        'sID'                             => '',
        'ACQBIN'                          => '',
        'Ecom_Payment_Card_ExpDate_Year'  => '',
        'Ecom_Payment_Card_ExpDate_Month' => '',
        'isHPPCall'                       => '',
        'EXTRA_CARDBRAND'                 => '',
        'MaskedPan'                       => '',
        'acqStan'                         => '',
        'EXTRA_UCAFINDICATOR'             => '',
        'clientIp'                        => '',
        'trantype'                        => '',
        'protocol'                        => '',
        'iReqDetail'                      => '',
        'md'                              => '',
        'ProcReturnCode'                  => '',
        'instalment'                      => '',
        'payResults_dsId'                 => '',
        'vendorCode'                      => '',
        'TransId'                         => '',
        'EXTRA_TRXDATE'                   => '',
        'signature'                       => '',
        'storetype'                       => '',
        'iReqCode'                        => '',
        'veresEnrolledStatus'             => '',
        'Response'                        => '',
        'SettleId'                        => '',
        'orgHash'                         => '',
        'mdErrorMsg'                      => '',
        'ErrMsg'                          => '',
        'PAResVerified'                   => '',
        'shopurl'                         => '',
        'cavv'                            => '',
        'digest'                          => '',
        'HostRefNum'                      => '',
        'callbackCall'                    => '',
        'AuthCode'                        => '',
        'cavvAlgorithm'                   => '',
        'xid'                             => '',
        'encoding'                        => '',
        'currency'                        => '',
        'mdStatus'                        => '',
        'dsId'                            => '',
        'eci'                             => '',
        'version'                         => '',
        'orgRnd'                          => '',
        'EXTRA_CARDISSUER'                => '',
        'clientid'                        => '',
        'txstatus'                        => '',
        'HASH'                            => '',
        'rnd'                             => '',
        'HASHPARAMS'                      => '',
        'HASHPARAMSVAL'                   => '',
        'hashAlgorithm'                   => '',
        'hc_active'                       => '',
    );

    /**
     * Class constructor
     *
     * @param int|object|array $data Item ID to load from the Database or a Nestpay_Transaction object.
     */
    public function __construct( $data = 0 ) {
        parent::__construct( $data );

        if ( $data instanceof Nestpay_Transaction ) {
            $this->set_id( absint( $data->get_id() ) );
        } elseif ( is_numeric( $data ) ) {
            $this->set_id( $data );
        } elseif ( is_object( $data ) && ! empty( $data->ID ) ) {
            $this->set_id( $data->ID );
            $this->set_props( (array) $data, 'set' );
            $this->set_object_read();
        } elseif ( is_array( $data ) && empty( $data['ID'] ) ) {
            $this->set_props( $data, 'set' );
            $this->set_object_read();
        } else {
            $this->set_object_read();
        }

        $this->data_store = WC_Data_Store::load( 'nestpay-transaction' );

        if ( $this->get_id() > 0 ) {
            $this->data_store->read( $this );
        }
    }

    //phpcs:disable
    #region Getters 
    //phpcs:enable

    /**
     * Order ID getter
     *
     * Retrieves `real` Order ID.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_order_id( $context = 'view' ) {
        return $this->get_prop( 'order_id', $context );
    }

    /**
     * Order ID getter
     *
     * Retrieves formatted (filtered) Order ID.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_oid( $context = 'view' ) {
        return $this->get_prop( 'oid', $context );
    }

    /**
     * ReturnOid Getter
     *
     * Returned order ID, must same as oid
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_ReturnOid( $context = 'view' ) {
        return $this->get_prop( 'ReturnOid', $context );
    }

    /**
     * PAResSyntaxOK Getter
     *
     * If PARes validation is syntactically correct, the value is true. Otherwise value is false. "Y" or "N"
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_PAResSyntaxOK( $context = 'view' ) {
        return $this->get_prop( 'PAResSyntaxOK', $context );
    }

    /**
     * Refresh Time Getter
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_refreshtime( $context = 'view' ) {
        return $this->get_prop( 'refreshtime', $context );
    }

    /**
     * Language Getter
     *
     * Language of the payment pages hosted by NestPay.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_lang( $context = 'view' ) {
        return $this->get_prop( 'lang', $context );
    }

    /**
     * Merchant ID Getter
     *
     * MPI Merchant ID
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_merchantID( $context = 'view' ) {
        return $this->get_prop( 'merchantID', $context );
    }

    /**
     * Masked Credit Card Getter
     *
     * Masked Credit Card Number
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_maskedCreditCard( $context = 'view' ) {
        return $this->get_prop( 'maskedCreditCard', $context );
    }

    /**
     * Amount Getter
     *
     * Transaction amount.
     * Uses "." as decimal separator
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return float|string    Float if viewing, string if editing
     */
    public function get_amount( $context = 'view' ) {
        $amount = $this->get_prop( 'amount', $context );

        return 'view' === $context ? wc_format_decimal( $amount, 2 ) : $amount;
    }

    /**
     * SID Getter
     *
     * Schema ID "1" for Visa, "2" for Mastercard
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_sID( $context = 'view' ) {
        return $this->get_prop( 'sID', $context );
    }

    /**
     * ACQBIN Getter
     *
     * This code identifies the financial institution acting as the acquirer of this customer transaction
     * The acquirer is the member or system user that signed the merchant or ADM or dispensed cash.
     * This number is usually a Visa-assigned BIN (Bank Identification Number).
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_ACQBIN( $context = 'view' ) {
        return $this->get_prop( 'ACQBIN', $context );
    }

    /**
     * Ecom_Payment_Card_Exp_Date Getter
     *
     * Two digit expiry year of the card.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_Ecom_Payment_Card_ExpDate_Year( $context = 'view' ) {
        return $this->get_prop( 'Ecom_Payment_Card_ExpDate_Year', $context );
    }

    /**
     * Ecom_Payment_Card_Exp_Date Getter
     *
     * Two digit expiry month of the card.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_Ecom_Payment_Card_ExpDate_Month( $context = 'view' ) {
        return $this->get_prop( 'Ecom_Payment_Card_ExpDate_Month', $context );
    }

    /**
     * IsHPPCall Getter
     *
     * If the transaction is a HPP call, the value is true. Otherwise value is false.
     * HPP Call is a transaction that is made on the external payment page.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string          YES if the transaction was made on the hosted page, NO if it was made on the merchant's site.
     */
    public function get_isHPPCall( $context = 'view' ) {
        return $this->get_prop( 'isHPPCall', $context );
    }

    /**
     * EXTRA_CARDBRAND Getter
     *
     * Brand of the card used for the transaction.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_EXTRA_CARDBRAND( $context = 'view' ) {
        return $this->get_prop( 'EXTRA_CARDBRAND', $context );
    }

    /**
     * MaskedPan Getter
     *
     * PAN masking hides a portion of the long card number on a credit or debit card.
     * This protects the card account numbers when displaying or printing them.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     *
     * @see https://www.eckoh.com/resources/glossary/masking
     */
    public function get_MaskedPan( $context = 'view' ) {
        return $this->get_prop( 'MaskedPan', $context );
    }

    /**
     * AcqStan Getter
     *
     * Acquirer-specific transaction reference number.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_acqStan( $context = 'view' ) {
        return $this->get_prop( 'acqStan', $context );
    }

    /**
     * UCAFINDICATOR Getter
     *
     * Universal Cardholder Authentication Field indicator.
     * It refers to information collected in online transactions, specifically relating to Mastercard's “SecureCode” program.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_EXTRA_UCAFINDICATOR( $context = 'view' ) {
        return $this->get_prop( 'EXTRA_UCAFINDICATOR', $context );
    }

    /**
     * ClientIp Getter
     *
     * Client IP address.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_clientIp( $context = 'view' ) {
        return $this->get_prop( 'clientIp', $context );
    }

    /**
     * TranType Getter
     *
     * Transaction type. "Auth" for authorization, “PreAuth” for preauthorization
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_trantype( $context = 'view' ) {
        return $this->get_prop( 'trantype', $context );
    }

    /**
     * Protocol Getter
     *
     * 3DS Protocol version
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_protocol( $context = 'view' ) {
        return $this->get_prop( 'protocol', $context );
    }

    /**
     * IReqDetail Getter
     *
     * May identify the specific data elements that caused the Invalid Request Code (so never supplied if Invalid Request Code is omitted).
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_iReqDetail( $context = 'view' ) {
        return $this->get_prop( 'iReqDetail', $context );
    }

    /**
     * MD Getter
     *
     * MPI data replacing card number
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_md( $context = 'view' ) {
        return $this->get_prop( 'md', $context );
    }

    /**
     * ProcReturnCode Getter
     *
     * Transaction status code.
     * Values are:
     * - 00 for authorized transactions
     * - 99 for gateway errors
     * - ISO-8583 error codes
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     *
     * @see https://neapay.com/post/iso8583-response-codes-for-transaction-processing_100.html
     */
    public function get_ProcReturnCode( $context = 'view' ) {
        return $this->get_prop( 'ProcReturnCode', $context );
    }

    /**
     * Instalment Getter
     *
     * Instalment count
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_instalment( $context = 'view' ) {
        return $this->get_prop( 'instalment', $context );
    }

    /**
     * PayResults_DsId
     *
     * Direct Service ID
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_payResults_dsId( $context = 'view' ) {
        return $this->get_prop( 'payResults_dsId', $context );
    }

    /**
     * VendorCode Getter
     *
     * Error message describing iReqDetail error.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_vendorCode( $context = 'view' ) {
        return $this->get_prop( 'vendorCode', $context );
    }

    /**
     * TransId Getter
     *
     * NestPay Transaction ID
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_TransId( $context = 'view' ) {
        return $this->get_prop( 'TransId', $context );
    }

    /**
     * TrxDate Getter
     *
     * Transaction date and time
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_EXTRA_TRXDATE( $context = 'view' ) {
        return $this->get_prop( 'EXTRA_TRXDATE', $context );
    }

    /**
     * Signature Getter
     *
     * Transaction signature
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_signature( $context = 'view' ) {
        return $this->get_prop( 'signature', $context );
    }

    /**
     * StoreType Getter
     *
     * Merchant payment model.
     * Possible values: "pay_hosting", “3d_pay”, "3d", "3d_pay_hosting"
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_storetype( $context = 'view' ) {
        return $this->get_prop( 'storetype', $context );
    }

    /**
     * IReqCode Getter
     *
     * Code provided by ACS indicating data that is formatted correctly, but which invalidates the request.
     * This element is included when business processing cannot be performed for some reason.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string          Empty string if transaction valid, error code otherwise.
     */
    public function get_iReqCode( $context = 'view' ) {
        return $this->get_prop( 'iReqCode', $context );
    }

    /**
     * VERES Getter
     *
     * **VER**ify **E**nrollment **S**tatus
     * Indicates whether the cardholder participates in Verified by Visa
     * Value of VERes enrollement status:
     * * Y  Authentication available.
     * * N  Not enrolled.
     * * U  Authentication unavailable.
     * * /  Value is not available due to errors.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_veresEnrolledStatus( $context = 'view' ) {
        return $this->get_prop( 'veresEnrolledStatus', $context );
    }

    /**
     * Response Getter
     *
     * Payment status.
     * Possible values:
     * - Approved
     * - Error
     * - Declined
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return string
     */
    public function get_Response( $context = 'view' ) {
        return $this->get_prop( 'Response', $context );
    }

    /**
     * SettleId Getter
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_SettleId( $context = 'view' ) {
        return $this->get_prop( 'SettleId', $context );
    }

    /**
     * OrgHash Getter
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_orgHash( $context = 'view' ) {
        return $this->get_prop( 'orgHash', $context );
    }

    /**
     * MdErrorMsg Getter
     *
     * Error Message from MPI (if any)
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_mdErrorMsg( $context = 'view' ) {
        return $this->get_prop( 'mdErrorMsg', $context );
    }

    /**
     * ErrMsg Getter
     *
     * Error message
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_ErrMsg( $context = 'view' ) {
        return $this->get_prop( 'ErrMsg', $context );
    }

    /**
     * PAResVerified Getter
     *
     * If signature validation of the return message is successful, the value is true.
     * If PARes message is not received or signature validation fails, the value is false. "Y" or "N"
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_PAResVerified( $context = 'view' ) {
        return $this->get_prop( 'PAResVerified', $context );
    }

    /**
     * ShopUrl Getter
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_shopurl( $context = 'view' ) {
        return $this->get_prop( 'shopurl', $context );
    }

    /**
     * CAVV Getter
     *
     * Cardholder Authentication Verification Value, determined by ACS.
     * 28 characters, contains a 20 byte value that has been Base64 encoded, giving a 28 byte result.
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_cavv( $context = 'view' ) {
        return $this->get_prop( 'cavv', $context );
    }

    /**
     * Digest Getter
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_digest( $context = 'view' ) {
        return $this->get_prop( 'digest', $context );
    }

    /**
     * HostRefNum Getter
     *
     * Host reference number
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_HostRefNum( $context = 'view' ) {
        return $this->get_prop( 'HostRefNum', $context );
    }

    /**
     * CallbackCall Getter
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_callbackCall( $context = 'view' ) {
        return $this->get_prop( 'callbackCall', $context );
    }

    /**
     * AuthCode Getter
     *
     * Transaction Verification/Approval/Authorization code
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_AuthCode( $context = 'view' ) {
        return $this->get_prop( 'AuthCode', $context );
    }

    /**
     * CavvAlgorithm Getter
     *
     * CAVV algorithm.
     * Possible values "0", "1", "2", "3"
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_cavvAlgorithm( $context = 'view' ) {
        return $this->get_prop( 'cavvAlgorithm', $context );
    }

    /**
     * Xid Getter
     *
     * Internet transaction identifier
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_xid( $context = 'view' ) {
        return $this->get_prop( 'xid', $context );
    }

    /**
     * Encoding Getter
     *
     * CodePage used for communication
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_encoding( $context = 'view' ) {
        return $this->get_prop( 'encoding', $context );
    }

    /**
     * Currency Getter
     *
     * Numeric Currency Code
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_currency( $context = 'view' ) {
        return $this->get_prop( 'currency', $context );
    }

    /**
     * MdStatus Getter
     *
     * Status code for the 3D transaction.
     * - 1: authenticated transaction
     * - 2, 3, 4: Card not participating
     * - 5,6,7,8: Authentication not available / system error
     * - 0: Authentication failed
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_mdStatus( $context = 'view' ) {
        return $this->get_prop( 'mdStatus', $context );
    }

    /**
     * DsID Getter
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_dsId( $context = 'view' ) {
        return $this->get_prop( 'dsId', $context );
    }

    /**
     * ECI Getter
     *
     * Electronic Commerce Indicator. empty for non-3D transactions
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_eci( $context = 'view' ) {
        return $this->get_prop( 'eci', $context );
    }

    /**
     * Version Getter
     *
     * MPI version information 3 characters (like `2.0`)
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_version( $context = 'view' ) {
        return $this->get_prop( 'version', $context );
    }

    /**
     * OrgRnd Getter
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_orgRnd( $context = 'view' ) {
        return $this->get_prop( 'orgRnd', $context );
    }

    /**
     * EXTRA_CARDISSUER Getter
     *
     * Payment Card issuer
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_EXTRA_CARDISSUER( $context = 'view' ) {
        return $this->get_prop( 'EXTRA_CARDISSUER', $context );
    }

    /**
     * ClientId Getter
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_clientid( $context = 'view' ) {
        return $this->get_prop( 'clientid', $context );
    }

    /**
     * TxStatus Getter
     *
     * 3D status for archival.
     * Possible values "A", "N", "Y"
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_txstatus( $context = 'view' ) {
        return $this->get_prop( 'txstatus', $context );
    }

    /**
     * HASH Getter
     *
     * Transaction Hash
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_HASH( $context = 'view' ) {
        return $this->get_prop( 'HASH', $context );
    }

    /**
     * Rnd Getter
     *
     * Random String used to generate the hash
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_rnd( $context = 'view' ) {
        return $this->get_prop( 'rnd', $context );
    }

    /**
     * HASHPARAMS Getter
     *
     * Pipe delimited string of parameters used to generate the HASH
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_HASHPARAMS( $context = 'view' ) {
        return $this->get_prop( 'HASHPARAMS', $context );
    }

    /**
     * HASHPARAMSVAL Getter
     *
     * Returns the Values used to generate the HASH
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_HASHPARAMSVAL( $context = 'view' ) {
        return $this->get_prop( 'HASHPARAMSVAL', $context );
    }

    /**
     * HashAlgorithm Getter
     *
     * Hash Algorithm version used.
     * Can be ver1 or ver2
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_hashAlgorithm( $context = 'view' ) {
        return $this->get_prop( 'hashAlgorithm', $context );
    }

    /**
     * HCActive Getter
     *
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    public function get_hc_active( $context = 'view' ) {
        return $this->get_prop( 'hc_active', $context );
    }

    //phpcs:disable
    #endregion Getters
    //phpcs:enable

    //phpcs:disable
    #region Setters
    //phpcs:enable

    /**
     * Set Order ID
     *
     * @param mixed $value Value to set.
     */
    public function set_order_id( $value ) {
        $this->set_prop( 'order_id', $value );
    }

    /**
     * Set Oid
     *
     * Oid is the nestpay order identifier
     *
     * @param mixed $value Value to set.
     */
    public function set_oid( $value ) {
        $this->set_prop( 'oid', $value );
    }

    /**
     * Set ReturnOid
     *
     * @param mixed $value Value to set.
     */
    public function set_ReturnOid( $value ) {
        $this->set_prop( 'ReturnOid', $value );
    }

    /**
     * Set PAResSytnaxOk
     *
     * @param mixed $value Value to set.
     */
    public function set_PAResSyntaxOK( $value ) {
        $this->set_prop( 'PAResSyntaxOK', $value );
    }

    /**
     * Set Refreshtime
     *
     * @param mixed $value Value to set.
     */
    public function set_refreshtime( $value ) {
        $this->set_prop( 'refreshtime', $value );
    }

    /**
     * Set lang
     *
     * @param mixed $value Value to set.
     */
    public function set_lang( $value ) {
        $this->set_prop( 'lang', $value );
    }

    /**
     * Set Merchant ID
     *
     * @param mixed $value Value to set.
     */
    public function set_merchantID( $value ) {
        $this->set_prop( 'merchantID', $value );
    }

    /**
     * Set masked credit card
     *
     * @param mixed $value Value to set.
     */
    public function set_maskedCreditCard( $value ) {
        $this->set_prop( 'maskedCreditCard', $value );
    }

    /**
     * Set order amount
     *
     * @param mixed $value Value to set.
     */
    public function set_amount( $value ) {
        $this->set_prop( 'amount', $value );
    }

    /**
     * Set sID
     *
     * @param mixed $value Value to set.
     */
    public function set_sID( $value ) {
        $this->set_prop( 'sID', $value );
    }

    /**
     * Set ACQBIN
     *
     * @param mixed $acqbin ACQBIN.
     */
    public function set_ACQBIN( $acqbin ) {
        $this->set_prop( 'ACQBIN', $acqbin );
    }

    /**
     * Set payment card expiration year
     *
     * @param mixed $exp_year Expiration year.
     */
    public function set_Ecom_Payment_Card_ExpDate_Year( $exp_year ) {
        $this->set_prop( 'Ecom_Payment_Card_ExpDate_Year', $exp_year );
    }

    /**
     * Set payment card expiration month
     *
     * @param mixed $exp_month Expiration month.
     */
    public function set_Ecom_Payment_Card_ExpDate_Month( $exp_month ) {
        $this->set_prop( 'Ecom_Payment_Card_ExpDate_Month', $exp_month );
    }

    /**
     * Set IsHPPCall
     *
     * @param mixed $is_hpp_call HPP Call flag.
     */
    public function set_isHPPCall( $is_hpp_call ) {
        $this->set_prop( 'isHPPCall', $is_hpp_call );
    }

    /**
     * Set card brand
     *
     * @param mixed $card_brand Card brand.
     */
    public function set_EXTRA_CARDBRAND( $card_brand ) {
        $this->set_prop( 'EXTRA_CARDBRAND', $card_brand );
    }

    /**
     * Set Masked PAN
     *
     * @param mixed $masked_pan Masked PAN.
     */
    public function set_MaskedPan( $masked_pan ) {
        $this->set_prop( 'MaskedPan', $masked_pan );
    }

    /**
     * Set ACQ Stan
     *
     * @param mixed $acq_stan ACQ Stan.
     */
    public function set_acqStan( $acq_stan ) {
        $this->set_prop( 'acqStan', $acq_stan );
    }

    /**
     * Set UCAF Indicator
     *
     * @param mixed $ucaf_indicator UCAF Indicator.
     */
    public function set_EXTRA_UCAFINDICATOR( $ucaf_indicator ) {
        $this->set_prop( 'EXTRA_UCAFINDICATOR', $ucaf_indicator );
    }

    /**
     * Set Client IP Address
     *
     * @param mixed $client_ip Client IP.
     */
    public function set_clientIp( $client_ip ) {
        $this->set_prop( 'clientIp', $client_ip );
    }

    /**
     * Set Transaction Type
     *
     * Can be PreAuth or Auth.
     *
     * @param mixed $transaction_type Transaction type.
     */
    public function set_trantype( $transaction_type ) {
        $this->set_prop( 'trantype', $transaction_type );
    }

    /**
     * Set Transaction Protocol
     *
     * @param mixed $protocol Transaction Protocol.
     */
    public function set_protocol( $protocol ) {
        $this->set_prop( 'protocol', $protocol );
    }

    /**
     * Set iReq Detail
     *
     * @param mixed $ireq_detail iReq Detail.
     */
    public function set_iReqDetail( $ireq_detail ) {
        $this->set_prop( 'iReqDetail', $ireq_detail );
    }

    /**
     * Set MD
     *
     * @param mixed $md MD.
     */
    public function set_md( $md ) {
        $this->set_prop( 'md', $md );
    }

    /**
     * Set Proc Return Code
     *
     * @param mixed $proc_return_code Proc Return Code.
     */
    public function set_ProcReturnCode( $proc_return_code ) {
        $this->set_prop( 'ProcReturnCode', $proc_return_code );
    }

    /**
     * Set Instalment number
     *
     * @param int $instalment Number of instalments.
     */
    public function set_instalment( $instalment ) {
        $this->set_prop( 'instalment', $instalment );
    }

    /**
     * Set Payment result DSID
     *
     * @param int $pay_results_dsid Payment result DSID.
     */
    public function set_payResults_dsId( $pay_results_dsid ) {
        $this->set_prop( 'payResults_dsId', $pay_results_dsid );
    }

    /**
     * Set Vendor Code
     *
     * @param mixed $vendor_code Vendor code.
     */
    public function set_vendorCode( $vendor_code ) {
        $this->set_prop( 'vendorCode', $vendor_code );
    }

    /**
     * Set Transaction ID
     *
     * @param mixed $transaction_id Transaction ID.
     */
    public function set_TransId( $transaction_id ) {
        $this->set_prop( 'TransId', $transaction_id );
    }

    /**
     * Set Transaction date
     *
     * @param string $transaction_date Transaction Date.
     */
    public function set_EXTRA_TRXDATE( $transaction_date ) {
        $this->set_prop( 'EXTRA_TRXDATE', $transaction_date );
    }

    /**
     * Set Transaction signature
     *
     * @param mixed $signature Transaction signature.
     */
    public function set_signature( $signature ) {
        $this->set_prop( 'signature', $signature );
    }

    /**
     * Set Store type
     *
     * @param mixed $store_type Store type.
     */
    public function set_storetype( $store_type ) {
        $this->set_prop( 'storetype', $store_type );
    }

    /**
     * Set iReq Code
     *
     * @param mixed $i_req_code iReq Code.
     */
    public function set_iReqCode( $i_req_code ) {
        $this->set_prop( 'iReqCode', $i_req_code );
    }

    /**
     * Set Veres enrolled status
     *
     * @param mixed $veres_enrolled_status Veres enrolled status.
     */
    public function set_veresEnrolledStatus( $veres_enrolled_status ) {
        $this->set_prop( 'veresEnrolledStatus', $veres_enrolled_status );
    }

    /**
     * Set Transaction response.
     *
     * Can be:
     *  - Approved
     *  - Declined
     *  - Error
     *
     * @param string $response Transaction response.
     */
    public function set_Response( $response ) {
        $this->set_prop( 'Response', $response );
    }

    /**
     * Set Settlement ID
     *
     * @param mixed $settlement_id Settlement ID.
     */
    public function set_SettleId( $settlement_id ) {
        $this->set_prop( 'SettleId', $settlement_id );
    }

    /**
     * Set Organisation Hash
     *
     * @param mixed $organisation_hash Organization hash.
     */
    public function set_orgHash( $organisation_hash ) {
        $this->set_prop( 'orgHash', $organisation_hash );
    }

    /**
     * Set MD Error message
     *
     * @param mixed $md_error_message MD error message.
     */
    public function set_mdErrorMsg( $md_error_message ) {
        $this->set_prop( 'mdErrorMsg', $md_error_message );
    }

    /**
     * Set Error message
     *
     * @param mixed $error_message Error message.
     */
    public function set_ErrMsg( $error_message ) {
        $this->set_prop( 'ErrMsg', $error_message );
    }

    /**
     * Set PAResVerified
     *
     * @param mixed $pares_verified PARes Verified.
     */
    public function set_PAResVerified( $pares_verified ) {
        $this->set_prop( 'PAResVerified', $pares_verified );
    }

    /**
     * Set Shop URL
     *
     * @param string $shop_url Shop URL.
     */
    public function set_shopurl( $shop_url ) {
        $this->set_prop( 'shopurl', $shop_url );
    }

    /**
     * Set CAVV
     *
     * @param mixed $cavv CAVV.
     */
    public function set_cavv( $cavv ) {
        $this->set_prop( 'cavv', $cavv );
    }

    /**
     * Set Digest
     *
     * @param mixed $digest Digest.
     */
    public function set_digest( $digest ) {
        $this->set_prop( 'digest', $digest );
    }

    /**
     * Set host reference number
     *
     * @param mixed $host_reference_number Host reference number.
     */
    public function set_HostRefNum( $host_reference_number ) {
        $this->set_prop( 'HostRefNum', $host_reference_number );
    }

    /**
     * Set CallbackCall.
     *
     * @param mixed $callback_call Callback call.
     */
    public function set_callbackCall( $callback_call ) {
        $this->set_prop( 'callbackCall', $callback_call );
    }

    /**
     * Set AuthCode.
     *
     * @param mixed $auth_code Auth Code.
     */
    public function set_AuthCode( $auth_code ) {
        $this->set_prop( 'AuthCode', $auth_code );
    }

    /**
     * Set CAVV Algorithm.
     *
     * @param mixed $cavv_algorithm Cavv algorithm.
     */
    public function set_cavvAlgorithm( $cavv_algorithm ) {
        $this->set_prop( 'cavvAlgorithm', $cavv_algorithm );
    }

    /**
     * Set XID
     *
     * @param mixed $xid XID.
     */
    public function set_xid( $xid ) {
        $this->set_prop( 'xid', $xid );
    }

    /**
     * Set Encoding
     *
     * @param string $encoding Character Encoding.
     */
    public function set_encoding( $encoding ) {
        $this->set_prop( 'encoding', $encoding );
    }

    /**
     * Set Currency
     *
     * @param int $currency Transaction currency.
     */
    public function set_currency( $currency ) {
        $this->set_prop( 'currency', $currency );
    }

    /**
     * Set MD Status
     *
     * @param mixed $md_status MD Status.
     */
    public function set_mdStatus( $md_status ) {
        $this->set_prop( 'mdStatus', $md_status );
    }

    /**
     * Set DS ID
     *
     * @param int $ds_id DS ID.
     */
    public function set_dsId( $ds_id ) {
        $this->set_prop( 'dsId', $ds_id );
    }

    /**
     * Set ECI
     *
     * @param mixed $eci ECI.
     */
    public function set_eci( $eci ) {
        $this->set_prop( 'eci', $eci );
    }

    /**
     * Set Version
     *
     * @param mixed $version Version.
     */
    public function set_version( $version ) {
        $this->set_prop( 'version', $version );
    }

    /**
     * Set OrgRnd
     *
     * @param mixed $org_rnd Organization random code.
     */
    public function set_orgRnd( $org_rnd ) {
        $this->set_prop( 'orgRnd', $org_rnd );
    }

    /**
     * Set Extra CardIssuer
     *
     * @param mixed $extra_cardissuer Extra Card Issuer.
     */
    public function set_EXTRA_CARDISSUER( $extra_cardissuer ) {
        $this->set_prop( 'EXTRA_CARDISSUER', $extra_cardissuer );
    }

    /**
     * Set client ID.
     *
     * @param mixed $client_id Client ID.
     */
    public function set_clientid( $client_id ) {
        $this->set_prop( 'clientid', $client_id );
    }

    /**
     * Set TX status.
     *
     * @param mixed $tx_status TX status.
     */
    public function set_txstatus( $tx_status ) {
        $this->set_prop( 'txstatus', $tx_status );
    }

    /**
     * Set RND.
     *
     * @param mixed $rnd RND.
     */
    public function set_rnd( $rnd ) {
        $this->set_prop( 'rnd', $rnd );
    }

    /**
     * Set HASH.
     *
     * @param mixed $hash Value to set.
     */
    public function set_HASH( $hash ) {
        $this->set_prop( 'HASH', $hash );
    }

    /**
     * Set Hash parameters
     *
     * @param mixed $hash_params Hash parameters.
     */
    public function set_HASHPARAMS( $hash_params ) {
        $this->set_prop( 'HASHPARAMS', $hash_params );
    }

    /**
     * Set Hash Params value
     *
     * @param string $hash_params_val Hash parameters value.
     */
    public function set_HASHPARAMSVAL( $hash_params_val ) {
        $this->set_prop( 'HASHPARAMSVAL', $hash_params_val );
    }

    /**
     * Set Hash algorithm
     *
     * @param mixed $hash_algorithm Hash algorithm.
     */
    public function set_hashAlgorithm( $hash_algorithm ) {
        $this->set_prop( 'hashAlgorithm', $hash_algorithm );
    }

    /**
     * Set hc_active
     *
     * @param mixed $hc_active HC Active.
     */
    public function set_hc_active( $hc_active ) {
        $this->set_prop( 'hc_active', $hc_active );
    }

    //phpcs:disable
    #endregion Setters
    //phpcs:enable
}
