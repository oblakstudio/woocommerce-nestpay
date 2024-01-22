<?php //phpcs:disable WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
/**
 * Core functions and utilities
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

use Oblak\NPG\WooCommerce_Nestpay;

/**
 * Returns the main instance of WCNPG
 *
 * @return WooCommerce_Nestpay
 */
function WCNPG() {
    return WooCommerce_Nestpay::instance();
}


/**
 * Get endpoints for a specific bank
 *
 * @param  string $bank Bank identifier.
 * @param  string $type Bank type. Can be `production` or `test`.
 * @return array        Bank endpoints.
 */
function nestpay_get_endpoints( string $bank, string $type = 'production' ): array {
    $deets = array(
        'production' => array(
			'api' => '',
			'bib' => '',
		),
        'test'       => array(
			'api' => '',
			'bib' => '',
		),
    );

    switch ( $bank ) {
        case 'intesa-rs':
            $deets = array(
                'production' => array(
                    'api' => 'https://bib.eway2pay.com/fim/api',
                    'bib' => 'https://bib.eway2pay.com/fim/est3Dgate',
                ),
                'test'       => array(
                    'api' => 'https://testsecurepay.eway2pay.com/fim/api',
                    'bib' => 'https://testsecurepay.eway2pay.com/fim/est3Dgate',
                ),
			);
            break;
    }

    /**
     * Filter the bank endpoints
     *
     * @param  array  $deets Bank endpoints.
     * @param  string $bank  Bank name.
     * @param  string $type  Bank type (production or test).
     * @return array         Modified bank endpoints.
     *
     * @since 2.2.2
     */
    return apply_filters( 'woocommerce_nestpay_get_bank_endpoints', $deets[ $type ], $bank, $type ); //phpcs:ignore WooCommerce.Commenting.HookComment.ParamCommentMissing
}
