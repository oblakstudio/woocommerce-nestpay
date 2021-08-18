<?php
namespace Oblak\NPG;

class CurrencyFix {

    private bool $store_rsd_fix;

    public function __construct() {
 
        $this->store_rsd_fix = get_option('woocommerce_nestpay_settings', false)['store_rsd_fix'] ?? false;

        // RSD fix
        add_filter('woocommerce_currency_symbol', [$this, 'fixCurrencySymbol'], 999, 2);

    }

    public function fixCurrencySymbol($currency_symbol, $currency) : string {

        if (!$this->store_rsd_fix) {
            return $currency_symbol;
        }

        return ( in_array($currency, ['RSD', 'РСД']) )
            ? 'RSD'
            : $currency;


    }

}
