<?php
namespace Oblak\NPG;

use Exception;
use Oblak\NPG\Scripts;
use Oblak\NPG\Ajax\ApiActions;
use Oblak\NPG\Admin\OrderActions;
use Oblak\NPG\Admin\EmailManager;

use const OBLAK\NPG\BASENAME;

class Bootstrap {

    /**
     * Singleton class instance
     * @var Bootstrap
     */
    private static $instance = null;

    private function __construct() {

        add_action('init', [$this, 'loadTextdomain']);
        add_action('plugins_loaded', [$this, 'initPlugin']);

        // Links and meta
        add_filter('plugin_action_links_' . BASENAME, [$this, 'addPluginLinks']);
        add_filter('plugin_row_meta', [$this, 'addPluginMeta'], 10, 2);

        // Initialize gateway
        add_filter('woocommerce_payment_gateways', [$this, 'addPaymentGateway'], 50, 1);

    }

    private function __clone() {
        throw new Exception('Singletons cannot be cloned');
    }

    /**
     * Gets the class instance
     *
     * @return Bootstrap
     */
    public static function instance() {
        return (self::$instance === null)
            ? self::$instance = new Bootstrap
            : self::$instance;
    }

    public function loadTextdomain() : void {

        $status = load_plugin_textdomain(
            'woocommerce-nestpay',
            false,
            dirname(BASENAME).'/languages'
        );

    }

    public function initplugin() {

        new EmailManager();
        new OrderActions();
        new CurrencyFix();
        new ApiActions();
        new Scripts();

    }

    public function addPluginLinks(array $links) : array {

        $links[] = sprintf(
            '<a href="%s">%s</a>',
            admin_url('admin.php?page=wc-settings&tab=checkout&section=nestpay'),
            __('Settings', 'woocommerce'),
        );

        return $links;

    }

    public function addPluginMeta(array $links, string $file) : array {

        if ($file != BASENAME) {
            return $links;
        }

        unset($links[2]);

        $links[] = sprintf(
            '<a href="%s" class="thickbox open-plugin-details-modal">%s</a>',
            admin_url('plugin-install.php?tab=plugin-information&amp;plugin=woocommerce-nestpay-payment-gateway&amp;TB_iframe=true&amp;width=600&amp;height=550'),
            __('View details', 'woocommerce-nestpay'),
        );

        $links[] = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            'https://docs.oblak.studio/woocommerce-nestpay',
            __('Docs', 'woocommerce-nestpay'),
        );

        $links[] = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            'https://podrska.oblak.studio/',
            __('Support', 'woocommerce-nestpay'),
        );

        return $links;

    }

    /**
     * Adds our Payment Gateway to list of WooCommerce Gateways
     *
     * @param  array $gateways List of gateways
     * @return array           Modified list of gateways
     */
    public function addPaymentGateway(array $gateways) : array {

        $gateways[] = '\\Oblak\NPG\\Gateway\\NestPayGateway';

        return $gateways;

    }

}
