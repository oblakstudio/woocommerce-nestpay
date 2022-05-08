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
     * Order ID Setter
     *
     * @param mixed $value Value to set.
     */
    public function set_order_id( $value ) {
        $this->set_prop( 'order_id', $value );
    }

    /**
     * Oid Setter
     *
     * @param mixed $value Value to set.
     */
    public function set_oid( $value ) {
        $this->set_prop( 'oid', $value );
    }

    /**
     * ReturnOid Setter
     *
     * @param mixed $value Value to set.
     */
    public function set_ReturnOid( $value ) {
        $this->set_prop( 'ReturnOid', $value );
    }

    /**
     * PAResSytnaxOk Setter
     *
     * @param mixed $value Value to set.
     */
    public function set_PAResSyntaxOK( $value ) {
        $this->set_prop( 'PAResSyntaxOK', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_refreshtime( $value ) {
        $this->set_prop( 'refreshtime', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_lang( $value ) {
        $this->set_prop( 'lang', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_merchantID( $value ) {
        $this->set_prop( 'merchantID', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_maskedCreditCard( $value ) {
        $this->set_prop( 'maskedCreditCard', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_amount( $value ) {
        $this->set_prop( 'amount', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_sID( $value ) {
        $this->set_prop( 'sID', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_ACQBIN( $value ) {
        $this->set_prop( 'ACQBIN', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_Ecom_Payment_Card_ExpDate_Year( $value ) {
        $this->set_prop( 'Ecom_Payment_Card_ExpDate_Year', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_Ecom_Payment_Card_ExpDate_Month( $value ) {
        $this->set_prop( 'Ecom_Payment_Card_ExpDate_Month', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_isHPPCall( $value ) {
        $this->set_prop( 'isHPPCall', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_EXTRA_CARDBRAND( $value ) {
        $this->set_prop( 'EXTRA_CARDBRAND', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_MaskedPan( $value ) {
        $this->set_prop( 'MaskedPan', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_acqStan( $value ) {
        $this->set_prop( 'acqStan', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_EXTRA_UCAFINDICATOR( $value ) {
        $this->set_prop( 'EXTRA_UCAFINDICATOR', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_clientIp( $value ) {
        $this->set_prop( 'clientIp', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_trantype( $value ) {
        $this->set_prop( 'trantype', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_protocol( $value ) {
        $this->set_prop( 'protocol', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_iReqDetail( $value ) {
        $this->set_prop( 'iReqDetail', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_md( $value ) {
        $this->set_prop( 'md', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_ProcReturnCode( $value ) {
        $this->set_prop( 'ProcReturnCode', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_instalment( $value ) {
        $this->set_prop( 'instalment', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_payResults_dsId( $value ) {
        $this->set_prop( 'payResults_dsId', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_vendorCode( $value ) {
        $this->set_prop( 'vendorCode', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_TransId( $value ) {
        $this->set_prop( 'TransId', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_EXTRA_TRXDATE( $value ) {
        $this->set_prop( 'EXTRA_TRXDATE', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_signature( $value ) {
        $this->set_prop( 'signature', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_storetype( $value ) {
        $this->set_prop( 'storetype', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_iReqCode( $value ) {
        $this->set_prop( 'iReqCode', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_veresEnrolledStatus( $value ) {
        $this->set_prop( 'veresEnrolledStatus', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_Response( $value ) {
        $this->set_prop( 'Response', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_SettleId( $value ) {
        $this->set_prop( 'SettleId', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_orgHash( $value ) {
        $this->set_prop( 'orgHash', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_mdErrorMsg( $value ) {
        $this->set_prop( 'mdErrorMsg', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_ErrMsg( $value ) {
        $this->set_prop( 'ErrMsg', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_PAResVerified( $value ) {
        $this->set_prop( 'PAResVerified', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_shopurl( $value ) {
        $this->set_prop( 'shopurl', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_cavv( $value ) {
        $this->set_prop( 'cavv', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_digest( $value ) {
        $this->set_prop( 'digest', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_HostRefNum( $value ) {
        $this->set_prop( 'HostRefNum', $value );
    }

    /**
     * CallbackCall setter
     *
     * @param mixed $value Value to set.
     * @todo Set proper wc_string_to_bool handling
     */
    public function set_callbackCall( $value ) {
        $this->set_prop( 'callbackCall', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_AuthCode( $value ) {
        $this->set_prop( 'AuthCode', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_cavvAlgorithm( $value ) {
        $this->set_prop( 'cavvAlgorithm', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_xid( $value ) {
        $this->set_prop( 'xid', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_encoding( $value ) {
        $this->set_prop( 'encoding', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_currency( $value ) {
        $this->set_prop( 'currency', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_mdStatus( $value ) {
        $this->set_prop( 'mdStatus', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_dsId( $value ) {
        $this->set_prop( 'dsId', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_eci( $value ) {
        $this->set_prop( 'eci', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_version( $value ) {
        $this->set_prop( 'version', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_orgRnd( $value ) {
        $this->set_prop( 'orgRnd', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_EXTRA_CARDISSUER( $value ) {
        $this->set_prop( 'EXTRA_CARDISSUER', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_clientid( $value ) {
        $this->set_prop( 'clientid', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_txstatus( $value ) {
        $this->set_prop( 'txstatus', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_HASH( $value ) {
        $this->set_prop( 'HASH', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_rnd( $value ) {
        $this->set_prop( 'rnd', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_HASHPARAMS( $value ) {
        $this->set_prop( 'HASHPARAMS', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_HASHPARAMSVAL( $value ) {
        $this->set_prop( 'HASHPARAMSVAL', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_hashAlgorithm( $value ) {
        $this->set_prop( 'hashAlgorithm', $value );
    }

    /**
     *
     * @param mixed $value Value to set.
     */
    public function set_hc_active( $value ) {
        $this->set_prop( 'hc_active', $value );
    }

    //phpcs:disable
    #endregion Setters
    //phpcs:enable
}
