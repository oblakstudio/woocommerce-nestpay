<?php
/**
 * ApiResponse
 *
 * DTO class which extends WC_Data to be used in the DataStore
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */
namespace Oblak\NPG\Gateway;

use WC_Data;

/**
 * Undocumented class
 *
 * @uses WC_Data
 */
class ApiResponse extends WC_Data {
    protected $id = 0;

    protected $data = [
        'id'                              => '',
        'processed'                       => '',
        'oid'                             => '',
        'trantype'                        => '',
        'amount'                          => '',
        'currency'                        => '',
        'Response'                        => '',
        'ProcReturnCode'                  => '',
        'mdStatus'                        => '',
        'ErrMsg'                          => '',
        'AUTH_CODE'                       => '',
        'TransId'                         => '',
        'TRANID'                          => '',
        'clientIp'                        => '',
        'email'                           => '',
        'tel'                             => '',
        'description'                     => '',
        'BillToCompany'                   => '',
        'BillToName'                      => '',
        'BillToStreet1'                   => '',
        'BillToStreet2'                   => '',
        'BillToCity'                      => '',
        'BillToStateProv'                 => '',
        'BillToPostalCode'                => '',
        'BillToCountry'                   => '',
        'ShipToCompany'                   => '',
        'ShipToName'                      => '',
        'ShipToStreet1'                   => '',
        'ShipToStreet2'                   => '',
        'ShipToCity'                      => '',
        'ShipToStateProv'                 => '',
        'ShipToPostalCode'                => '',
        'ShipToCountry'                   => '',
        'DimCriteria1'                    => '',
        'DimCriteria2'                    => '',
        'DimCriteria3'                    => '',
        'DimCriteria4'                    => '',
        'DimCriteria5'                    => '',
        'DimCriteria6'                    => '',
        'DimCriteria7'                    => '',
        'DimCriteria8'                    => '',
        'DimCriteria9'                    => '',
        'DimCriteria10'                   => '',
        'comments'                        => '',
        'instalment'                      => '',
        'INVOICENUMBER'                   => '',
        'storetype'                       => '',
        'lang'                            => '',
        'xid'                             => '',
        'HostRefNum'                      => '',
        'ReturnOid'                       => '',
        'MaskedPan'                       => '',
        'rnd'                             => '',
        'merchantID'                      => '',
        'txstatus'                        => '',
        'iReqCode'                        => '',
        'iReqDetail'                      => '',
        'PAResSyntaxOK'                   => '',
        'PAResVerified'                   => '',
        'eci'                             => '',
        'cavv'                            => '',
        'cavvAlgorthm'                    => '',
        'md'                              => '',
        'Version'                         => '',
        'sID'                             => '',
        'mdErrorMsg'                      => '',
        'clientid'                        => '',
        'EXTRA_TRXDATE'                   => '',
        'ACQBIN'                          => '',
        'acqStan'                         => '',
        'cavvAlgorithm'                   => '',
        'digest'                          => '',
        'dsId'                            => '',
        'Ecom_Payment_Card_ExpDate_Month' => '',
        'Ecom_Payment_Card_ExpDate_Year'  => '',
        'EXTRA_CARDBRAND'                 => '',
        'EXTRA_CARDISSUER'                => '',
        'EXTRA_INVOICENUMBER'             => '',
        'failUrl'                         => '',
        'HASH'                            => '',
        'hashAlgorithm'                   => '',
        'HASHPARAMS'                      => '',
        'HASHPARAMSVAL'                   => '',
        'okurl'                           => '',
        'refreshtime'                     => '',
        'SettleId'                        => '',
        'created_at'                      => '',
        'updated_at'                      => '',
    ];
}
