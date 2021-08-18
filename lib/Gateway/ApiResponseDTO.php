<?php
namespace Oblak\NPG\Gateway;

class ApiResponseDTO {

    /**
     * Plugin API Operation status
     *
     * @var string
     */
    public $apiOpStatus;

    public $ErrMsg;

    public $ProcReturnCode;

    public $Response;

    public $OrderId;

    public $TransId;

    public $AUTH_CODE;

    public $HOST_REF_NUM;

    public $CAVV_3D;

    public $AUTH_DTTM;

    public $MDSTATUS;

    public $ORD_ID;

    public $TRANS_ID;

    public $CHARGE_TYPE_CD;

    public $SETTLEID;

    public $TRXDATE;

    public $TRANS_STAT;

    public $CAPTURE_DTTM;

    public $ORIG_TRANS_AMT;

    public $CAPTURE_AMT;

    public $XID_3D;

    public $HOSTDATE;

    public $PROC_RET_CD;

    public $ECI_3D;

    public $PAN;

    public $NUMCODE;

    public $ORDERSTATUS;

    public function __construct(array $data) {

        $extra = $data['Extra'];
        unset($data['Extra']);

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        foreach ($extra as $key => $value) {
            $this->$key = $value;
        }

    }

    public function getTransactionStatus() : string {

        switch($this->TRANS_STAT) :

            case 'D' :
                $status =  __('Not succesful', 'woocommerce-nestpay');
            break;

            case 'A' :
                $status =  __('Preauthorization, not settled', 'woocommerce-nestpay');
            break;

            case 'C' :
                $status =  __('Capture, not Settled', 'woocommerce-nestpay');
            break;

            case 'S' :
                $status =  __('Deposited', 'woocommerce-nestpay');
            break;

            case 'R' :
                $status =  __('Reversal Required', 'woocommerce-nestpay');
            break;

            case 'V' :
                $status =  __('Voided', 'woocommerce-nestpay');
            break;

            case 'PN' :
                $status =  __('Pending', 'woocommerce-nestpay');
            break;

            case 'NW' :
                $status =  __('First Commit', 'woocommerce-nestpay');
            break;

            default :
                $status =  __('Unknown status', 'woocommerce-nestpay');
            break;

        endswitch;

        return $status;

    }

}
