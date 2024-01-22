<?php
/**
 * Nestpay_Gateway class file
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage Gateway
 */

namespace Oblak\NPG\WooCommerce\Gateway;

use Oblak\NPG\WooCommerce\Data\Nestpay_Transaction;
use WC_Order;
use WP_Error;

/**
 * Nestpay client for communication with Nestpay server
 */
class Nestpay_Client {

    /**
     * Base XML for API request
     *
     * @var string|null
     */
    private $base_xml = null;

    /**
     * Class constructor
     *
     * @param string $merchant_id Merchant ID recieved from the bank.
     * @param string $username    API Username.
     * @param string $password    API Password.
     * @param string $api_url     NestPay API URL.
     */
    public function __construct(
        /**
         * Merchant ID recieved from the bank
         *
         * @var string
         */
        private string $merchant_id,
        /**
         * API Username
         *
         * @var string
         */
        private string $username,
        /**
         * API Password
         *
         * @var string
         */
        private string $password,
        /**
         * NestPay API URL
         *
         * @var string
         */
        private string $api_url
    ) {
        $this->base_xml = $this->generate_base_xml();
    }

    /**
     * Generaes base XML for API request
     *
     * @return string XML String
     */
    private function generate_base_xml() {
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

    /**
     * Generates the XML for the API request
     *
     * @param  string $action       API Action. Can be `query`, `capture`, `void`, `refund`.
     * @param  string $order_number Order number.
     * @param  float  $amount       Amount to refund / capture.
     * @return string XML String    XML for the API request
     */
    private function generate_request_xml( $action, $order_number, $amount = 0 ) {
        $order_xml  = sprintf( '<OrderId>%s</OrderId>', $order_number );
        $action_xml = '';

        switch ( $action ) {
            case 'query':
                $action_xml = '<Extra><ORDERSTATUS>QUERY</ORDERSTATUS></Extra>';
                break;

            case 'capture':
                $action_xml = '<Type>PostAuth</Type>';
                break;

            case 'void':
                $action_xml = '<Type>Void</Type>';
                break;

            case 'refund':
                $action_xml = "<Type>Credit</Type><Total>{$amount}</Total>";
                break;
        }

        return sprintf( $this->base_xml, $order_xml, $action_xml );
    }

    /**
     * Parses XML response from NestPay and returns the Transaction object
     *
     * @param  string   $trantype  Transaction type.
     * @param  string   $response  Response XML string.
     * @param  WC_Order $order     Order object.
     * @param  float    $amount    Order amount.
     * @return Nestpay_Transaction Transaction object
     */
    private function parse_xml_response( $trantype, $response, $order, $amount ) {
        $xml  = simplexml_load_string( $response );
        $json = wp_json_encode( $xml );

        Nestpay_Gateway::log( 'Nestpay API response: ' . wc_print_r( $json, true ), 'debug' );

        $data = json_decode( $json, true );

        if ( array_key_exists( 'Extra', $data ) ) {
            foreach ( $data['Extra'] as $key => $value ) {
                $data[ "EXTRA_{$key}" ] = $value;
            }
            unset( $data['Extra'] );
        }

        foreach ( $data as $key => $value ) {
            if ( is_array( $value ) ) {
                Nestpay_Gateway::log( "Nestpay API response: {$key} is an array. Value: " . wc_print_r( $value, true ), 'debug' );
                $data[ $key ] = ! empty( $value ) ? maybe_serialize( $value ) : '';
            }
        }

        $classname   = WCNPG()->transaction_factory->get_transaction_classname( 0, 'api' );
        $transaction = new $classname( $data );

        $data['trantype'] ??= $trantype;
        $data['order_id']   = $order->get_id();
        $data['oid']        = $order->get_order_number();
        $data['amount']     = $amount;

        $transaction->set_props( $data, 'set' );

        $transaction->save();

        return $transaction;
    }

    /**
     * Sends request to NestPay API and returns the response
     *
     * @param  string   $transaction_type   Transaction type.
     * @param  WC_Order $order              Order object.
     * @param  string   $xml                XML string.
     * @param  float    $amount             Refund / Capture amount.
     * @return WP_Error|Nestpay_Transaction Transaction object if request was successful, WP_Error if not
     */
    private function send_request( $transaction_type, $order, $xml, $amount = 0 ) {
        Nestpay_Gateway::log( 'Transaction XML: ' . $xml, 'debug' );

        $raw_response = wp_safe_remote_post(
            $this->api_url,
            array(
				'method'      => 'POST',
				'headers'     => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
					'Accept'       => '*/*',
				),
				'user-agent'  => 'WooCommerce/' . WC()->version,
				'httpversion' => '1.1',
				'timeout'     => 70,
				'body'        => array( 'DATA' => $xml ),
            )
        );

        if ( is_wp_error( $raw_response ) ) {
            return $raw_response;
        }

        if ( empty( $raw_response['body'] ) ) {
            return new WP_Error( 'nestpay-api', 'Empty response' );
        }

        return $this->parse_xml_response( $transaction_type, $raw_response['body'], $order, $amount );
    }

    /**
     * Retrieves the transaction status
     *
     * @param  WC_Order $order              Order object.
     * @return WP_Error|Nestpay_Transaction Transaction object if request was successful, WP_Error if not
     */
    public function query_payment( $order ) {
        $request_xml = $this->generate_request_xml( 'query', $order->get_order_number() );

        return $this->send_request( 'Query', $order, $request_xml );
    }

    /**
     * Captures the payment
     *
     * Transfers the authorized funds from the NestPay account to the merchant's account.
     *
     * @param  WC_Order $order              Order object.
     * @param  float    $amount             Amount to capture.
     * @return WP_Error|Nestpay_Transaction Transaction object if request was successful, WP_Error if not
     */
    public function capture_payment( $order, $amount = 0 ) {
        if ( 0 === $amount ) {
            $amount = $order->get_total();
        }

        $request_xml = $this->generate_request_xml( 'capture', $order->get_order_number(), $amount );

        return $this->send_request( 'Capture', $order, $request_xml, $amount );
    }

    /**
     * Refunds the payment
     *
     * Transfers the authorized funds from the NestPay account to the merchant's account.
     *
     * @param  WC_Order $order              Order object.
     * @param  float    $amount             Amount to refund.
     * @return WP_Error|Nestpay_Transaction Transaction object if request was successful, WP_Error if not
     */
    public function refund_payment( $order, $amount ) {
        $request_xml = $this->generate_request_xml( 'refund', $order->get_order_number(), $amount );

        return $this->send_request( 'Refund', $order, $request_xml, $amount );
    }

    /**
     * Voids the payment
     *
     * @param  WC_Order $order              Order object.
     * @return WP_Error|Nestpay_Transaction Transaction object if request was successful, WP_Error if not
     */
    public function void_payment( $order ) {
        $request_xml = $this->generate_request_xml( 'void', $order->get_order_number() );

        return $this->send_request( 'Void', $order, $request_xml );
    }
}
