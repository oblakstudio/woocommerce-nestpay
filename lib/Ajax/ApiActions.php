<?php
namespace Oblak\NPG\Ajax;

use Exception;
use WC_Order;
use WC_Order_Refund;

use Oblak\NPG\Gateway\ApiClient;
use Oblak\NPG\Gateway\ApiResponseDTO;
use Oblak\NPG\Gateway\NestPayGateway;

class ApiActions {

    
    private static array $actions = [
        'nestpay_query'   => 'queryTransaction',
        'nestpay_capture' => 'captureTransaction',
        'nestpay_void'    => 'voidTransaction',
        // 'nestpay_refund'  => 'refundTransaction',
    ];

    private array $np_opts;

    public function __construct() {

        foreach (array_keys(self::$actions) as $action) {
            add_action("wp_ajax_{$action}", [$this, 'handleRequest']);
        }

    }

    /**
     *
     * @return WC_Order|WC_Order_Refund
     * @throws Exception
     */
    private function sanityCheck()
    {

        $order    = wc_get_order($_REQUEST['order_id']);
        $response = [
            'status'  => 0,
            'message' => __('You are not authorized to perform this operation', 'woocommerce-nestpay'),
        ];

        if (!check_admin_referer($_REQUEST['action'], 'nonce') || !current_user_can('manage_woocommerce') ) {
            wp_send_json($response, 401);
        }

        if ( !$order) {
            $response['message'] = __('Invalid Order ID', 'woocommerce-nestpay');
            wp_send_json($response, 404);
        }

        if ($order->get_payment_method() != 'nestpay') {
            $response['message'] = __('Order not paid with payment card', 'woocommerce-nestpay');
            wp_send_json($response, 403);
        }

        return $order;

    }

    public function handleRequest() : void {

        $order = $this->sanityCheck();
        $action = self::$actions[$_REQUEST['action']];

        $this->processAction($order, $action);

    }

    private function processAction(WC_Order &$order, string $action) : void {


        $this->np_opts = get_option('woocommerce_nestpay_settings');

        try {

        /** @var ApiResponseDTO $data */
        $data = $this->$action($order);

        wp_send_json([
            'status'  => 1,
            'message' => __('Action performed successfully', 'woocommerce-nestpay'),
            'data'    => $this->processResponse($data),
        ], 200);
        
        } catch (Exception $e) {

            wp_send_json([
                'status'  => 0,
                'message' => __('Error in communication with Bank', 'woocommerce-nestpay'),
            ], 500);

        }

    }

    private function queryTransaction(WC_Order &$order) : ApiResponseDTO {

        /** @var NestPayGateway $gateway */
        $gateway = wc_get_payment_gateway_by_order($order);

        $data = $gateway->query_payment($order->get_id());

        if ( is_wp_error($data) ) {
            throw new Exception();
        }

        return $data;

    }

    private function captureTransaction(WC_Order &$order) : ApiResponseDTO {

        /** @var NestPayGateway $gateway */
        $gateway = wc_get_payment_gateway_by_order($order);

        $gateway->capture_payment($order->get_id());

        return $order->get_meta('_nestpay_postauth', true);

    }

    private function voidTransaction(WC_Order &$order) : ApiResponseDTO {

        /** @var NestPayGateway $gateway */
        $gateway = wc_get_payment_gateway_by_order($order);

        $gateway->void_payment($order->get_id());

        return $order->get_meta('_nestpay_void', true);
    }

    private function processResponse(ApiResponseDTO $dto) : array {
        
        $unknown = __('Unknown', 'woocommerce-nestpay');

        $formatted = [
            'code'     => $dto->ProcReturnCode ?? $unknown,
            'response' => $dto->Response ?? $unknown,
            'orderID'  => $dto->OrderId ?? $unknown,
            'transID'  => $dto->TransId ?? $unknown,
            'date'     => $dto->HOSTDATE ?? $unknown,
            'status'   => $dto->getTransactionStatus(),
            'opStatus' => $dto->apiOpStatus,
        ];

        return $formatted;

    }

}
