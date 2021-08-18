<?php
namespace Oblak\NPG;

use Oblak\Asset\Loader;

use const OBLAK\NPG\PATH;

function assets_uri(string $asset) : string {
    return Loader::getInstance()->getUri('woocommerce-nestpay', $asset);
}

function getGatewayCurrencies()
{

    $currencies = wp_cache_get('currencies', 'woocommerce_nestpay');

    if ($currencies === false) :

        $currencies = json_decode(file_get_contents(PATH . '/config/currencies.json'), true);

        wp_cache_set('currencies', $currencies, 'woocommerce_nestpay', 86400);

    endif;

    $formatted = [];
    $formatted[0] = __('WooCommerce currency');

    foreach ($currencies as $code => $data) :

        $formatted[$data['currency_numeric_code']] = "{$code} - {$data['currency_name']}";

    endforeach;

    return $formatted;

}

function getCurrencyNumericCode($currency_code) {

    foreach (getGatewayCurrencies() as $code => $name) {

        if ( strpos($name, $currency_code) !== false ) {
            return $code;
        }

    }

}
