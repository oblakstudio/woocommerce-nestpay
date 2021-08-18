<?php
namespace Oblak\NPG\Gateway;

/**
 * Gateway Response Data Transfer Object
 * 
 * Describes BIB Gateway response on payment callback
 * @since 1.0.0
 */
class GwResponseDTO {

    /**
     * Returned order ID, must be the same as input orderId
     * @var string|int
     */
    public $ReturnOid;

    /**
     * Transaction ID
     * @var mixed
     */
    public $TRANID;

    public $PAResSyntaxOK;

    public $refreshtime;

    public $lang;

    public $merchantID;

    /**
     * Masked payment card number - Variant 1
     * 
     * @var string
     */
    public $maskedCreditCard;

    public $amount;

    public $sID;

    public $ACQBIN;

    /**
     * Credit cart expiration year
     * @var mixed
     */
    public $Ecom_Payment_Card_ExpDate_Year;

    /**
     * Credit cart expiration month
     * @var mixed
     */
    public $Ecom_Payment_Card_ExpDate_Month;

    public $isHPPCall;

    public $EXTRA_CARDBRAND;

    /**
     * Masked payment card number - Variant 2
     * @var string 12 characters, XXXXXX***XXX
     */
    public $MaskedPan;

    public $acqStan;

    public $EXTRA_UCAFINDICATOR;

    /**
     * IP address of the customer
     *
     * @var string
     */
    public $clientIp;

    public $trantype;

    public $protocol;

    public $iReqDetail;

    public $okUrl;

    public $md;

    /**
     * Transaction status code
     *
     * 00 for authorized transactions  
     * 99 for gateway errors  
     * Other codes for ISO-8583 errors
     *
     * @see https://en.wikipedia.org/wiki/ISO_8583
     * @var string
     */
    public $ProcReturnCode;

    public $instalment;

    public $payResults_dsId;

    public $vendorCode;

    /**
     * Nestpay Transaction Id
     *
     * @var string Maximum 64 characters
     */
    public $TransId;

    /**
     * Transaction Date
     *
     * 17 characters, formatted as "yyyyMMdd HH:mm:ss"
     *
     * @var string
     */
    public $EXTRA_TRXDATE;

    public $signature;

    public $storetype;

    public $iReqCode;

    public $veresEnrolledStatus;

    /**
     * Payment status
     *
     * @var string Approved|Error|Declined
     */
    public $Response;

    public $SettleId;

    public $orgHash;

    public $mdErrorMsg;

    /**
     * Error message
     * @var string Maximum 255 characters
     */
    public $ErrMsg;

    public $PAResVerified;

    public $shopurl;

    public $cavv;

    public $digest;

    /**
     * Host reference number
     * @var string 12 characters
     */
    public $HostRefNum;

    public $callbackCall;

    /**
     * Transaction Verification/Approval/Authorization code
     * @var string 6 character identifier
     */
    public $AuthCode;

    public $failUrl;

    public $cavvAlgorithm;

    /**
     * Internet transaction identifier
     * @var string 28 character identifier
     */
    public $xid;

    public $encoding;

    public $currency;

    /**
     * Order ID
     * @var string|integer 
     */
    public $oid;

    public $mdStatus;

    public $dsId;

    public $eci;

    public $version;

    public $orgRnd;

    public $EXTRA_CARDISSUER;

    public $clientid;

    public $txstatus;

    public $_charset_;

    /**
     * Hash value of HASHPARAMSVAL and merchant password field
     *
     * @var string
     */
    public $HASH;

    /**
     * Random string, will be used for hash comparison
     *
     * @var string
     */
    public $rnd;

    /**
     * Contains the field names used for hash calculation.
     *
     * Field names are appended with ":" character
     *
     * Possible values  
     * "clientid:oid:AuthCode:ProcReturnCod e:Response:rnd:" for non-3D transactions  
     * "clientId:oid:AuthCode:ProcReturnCod e:Response:mdStatus:cavv:eci:md:rnd:" for 3D transactions
     *
     * @var string
     */
    public $HASHPARAMS;

    /**
     * Contains the appended field values for hash calculation.
     * 
     * Field values appended with thesame order in HASHPARAMS field
     *
     * @var string
     */
    public $HASHPARAMSVAL;

    public function __construct(array $data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

}
