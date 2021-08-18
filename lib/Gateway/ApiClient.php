<?php
namespace Oblak\NPG\Gateway;

use GuzzleHttp\Psr7\Request;
use WP_Error;

class ApiClient
{

    private static ?ApiClient $instance = null;

    /**
     * Base XML for API request
     */
    private static ?string $base_xml = null;

    /**
     * Merchant ID recieved from the bank
     */
    private $merchant_id;

    /**
     * API Username
     */
    private $username;

    /**
     * API Password
     */
    private $password;

    private $api_url;

    private function __construct()
    {

        $opts   = get_option('woocommerce_nestpay_settings', false);
        $prefix = 'yes' === $opts['testmode'] ? 'test_' : '';

        $this->merchant_id = $opts[$prefix . 'merchant_id'];
        $this->username    = $opts[$prefix . 'username'];
        $this->password    = $opts[$prefix . 'password'];
        $this->api_url     = $opts[$prefix . 'api_url'];

        self::$base_xml = $this->generateBaseXML();

    }

    public static function getInstance() : ApiClient {
        return (self::$instance === null)
            ? self::$instance = new ApiClient()
            : self::$instance;
    }

    /**
     *
     * @return string XML String
     */
    private function generateBaseXML()
    {

        return sprintf(
            '<?xml version="1.0" encoding="UTF-8" ?>
            <CC5Request>
                <Name>%s</Name>
                <Password>%s</Password>
                <ClientId>%s</ClientId>
                %%s
                %%s
            </CC5Request>',
            $this->username,
            $this->password,
            $this->merchant_id
        );

    }

    private function parseXmlResponse(string $response) : ApiResponseDTO {

        $xml = simplexml_load_string($response);
        $json = json_encode($xml);

        return new ApiResponseDTO(json_decode($json, true));

    }

    private function getQueryRequest($order_id) {

        return sprintf(
            self::$base_xml,
            sprintf('<OrderId>%s</OrderId>', $order_id),
            '<Extra><ORDERSTATUS>QUERY</ORDERSTATUS></Extra>'
        );

    }

    private function getCaptureRequest($order_id) {

        return sprintf(
            self::$base_xml,
            sprintf('<OrderId>%s</OrderId>', $order_id),
            '<Type>PostAuth</Type>'
        );

    }

    private function getRefundRequest($order_id, $amount) {

        return sprintf(
            self::$base_xml,
            sprintf('<OrderId>%s</OrderId>', $order_id),
            "<Type>Credit</Type><Total>{$amount}</Total>",
        );

    }

    private function getVoidRequest($order_id) {

        return sprintf(
            self::$base_xml,
            sprintf('<OrderId>%s</OrderId>', $order_id),
            "<Type>Void</Type>",
        );

    }

    /**
     * Retrieves the transaction status
     *
     * @param  int                     $order_id Order ID we're querying
     * @return WP_Error|ApiResponseDTO           ResponseDTO if request was successful, WP_Error if not
     */
    public function queryPayment($order_id)
    {

        $raw_response = wp_safe_remote_post($this->api_url, [
            'method'      => 'POST',
            'headers'     => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept'       => '*/*'
            ],
            'user-agent'  => 'WooCommerce/' . WC()->version,
            'httpversion' => '1.1',
            'timeout'     => 70,
            'body'        => ['DATA' => $this->getQueryRequest($order_id)],
        ]);

        NestPayGateway::log('Query Transaction request: ' .  $this->getQueryRequest($order_id));
        NestPayGateway::log('Query Transaction response: ' . wc_print_r($raw_response, true));

        if (is_wp_error($raw_response)) {
            return $raw_response;
        }

        if ( empty($raw_response['body']) ) {
            return new WP_Error('nestpay-api', 'Empty response');
        }

        return $this->parseXmlResponse($raw_response['body']);
        
    }

    /**
     * Captures the payment
     * 
     * Transfers the authorized funds from the NestPay account to the merchant's account.
     *
     * @param  int                     $order_id Order ID we're querying
     * @return WP_Error|ApiResponseDTO           ResponseDTO if request was successful, WP_Error if not
     */
    public function capturePayment($order_id) {

        $raw_response = wp_safe_remote_post($this->api_url, [
            'method'      => 'POST',
            'headers'     => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept'       => '*/*'
            ],
            'user-agent'  => 'WooCommerce/' . WC()->version,
            'httpversion' => '1.1',
            'timeout'     => 70,
            'body'        => ['DATA' => $this->getCaptureRequest($order_id)],
        ]);

        NestPayGateway::log('Query Payment request: ' .  $this->getQueryRequest($order_id));
        NestPayGateway::log('Query Payment response: ' . wc_print_r($raw_response, true));

        if (is_wp_error($raw_response)) {
            return $raw_response;
        }

        if ( empty($raw_response['body']) ) {
            return new WP_Error('nestpay-api', 'Empty response');
        }

        return $this->parseXmlResponse($raw_response['body']);

    }

    /**
     * Refunds the payment
     * 
     * Transfers the authorized funds from the NestPay account to the merchant's account.
     *
     * @param  int                     $order_id Order ID we're querying
     * @param  float                   $amount   Amount to refund
     * @return WP_Error|ApiResponseDTO           ResponseDTO if request was successful, WP_Error if not
     */
    public function refundPayment(int $order_id, float $amount) {

        $raw_response = wp_safe_remote_post($this->api_url, [
            'method'      => 'POST',
            'headers'     => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept'       => '*/*'
            ],
            'user-agent'  => 'WooCommerce/' . WC()->version,
            'httpversion' => '1.1',
            'timeout'     => 70,
            'body'        => ['DATA' => $this->getRefundRequest($order_id, $amount)],
        ]);

        NestPayGateway::log('Refund Payment request: ' .  $this->getQueryRequest($order_id));
        NestPayGateway::log('Refund Payment response: ' . wc_print_r($raw_response, true));

        if (is_wp_error($raw_response)) {
            return $raw_response;
        }

        if ( empty($raw_response['body']) ) {
            return new WP_Error('nestpay-api', 'Empty response');
        }

        return $this->parseXmlResponse($raw_response['body']);

    }

    public function voidPayment(int $order_id) {

        $raw_response = wp_safe_remote_post($this->api_url, [
            'method'      => 'POST',
            'headers'     => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept'       => '*/*'
            ],
            'user-agent'  => 'WooCommerce/' . WC()->version,
            'httpversion' => '1.1',
            'timeout'     => 70,
            'body'        => ['DATA' => $this->getVoidRequest($order_id)],
        ]);

        NestPayGateway::log('Void Payment request: ' .  $this->getQueryRequest($order_id));
        NestPayGateway::log('Refund Payment response: ' . wc_print_r($raw_response, true));

        if (is_wp_error($raw_response)) {
            return $raw_response;
        }

        if ( empty($raw_response['body']) ) {
            return new WP_Error('nestpay-api', 'Empty response');
        }

        return $this->parseXmlResponse($raw_response['body']);

    }



    // public function voidTransaction($order_id)
    // {

    //     $data = '<Type>Void</Type>';

    //     return $this->sendRequest($data, $order_id);
    // }

    // public function refundTransaction($order_id, $amount)
    // {

    //     $data = "<Type>Credit</Type><Total>{$amount}</Total>";

    //     return $this->sendRequest($data, $order_id);

    // }

}
