<?php
namespace Oblak\NPG;

use Oblak\Asset\Loader;

use const OBLAK\NPG\PATH;

class Scripts
{

    public function __construct()
    {

        // if( !is_admin() ) {
        //     return;
        // }

        add_action('init', [$this, 'enqueueAssets']);
        add_filter('admin_body_class', [$this, 'addRouterClasses'], 9999);
        add_action('admin_footer', [$this, 'addLoaderTemplate'], 999, 1);


        add_action('woocommerce-nestpay/localize/admin.js', [$this, 'localizeScript']);

        add_action('woocommerce-nestpay/enqueue/main.js', [$this, 'checkEnqueueing'], 99);
        add_action('wp_enqueue_scripts', [$this, 'enqueueHCaptcha'], 99);

    }

    public function enqueueAssets() : void {

        Loader::getInstance()->registerNamespace(
            'woocommerce-nestpay',
            require_once PATH . 'config/assets.php'
        );

    }

    public function addRouterClasses(string $classes) : string {

        
        $page    = $_GET['page'] ?? '';
        $tab     = $_GET['tab'] ?? '';
        $section = $_GET['section'] ?? '';

        if ($page == 'wc-settings' && $tab == 'checkout' && $section == 'nestpay') { 
            $classes .= ' nestpay-settings ';
        }
        
        return $classes;

    }

    public function localizeScript() : void{

        wp_localize_script('woocommerce-nestpay/admin.js', 'nestpay', [
            'prompt'    => __('Enter amount to authorize, leave blank for full amount', 'woocommerce-nestpay'),
            'transCode' => __('Transaction code', 'woocommerce-nestpay'),
            'transID'   => __('Transaction ID', 'woocommerce-nestpay'),
            'orderID'   => __('Order ID', 'woocommerce-nestpay'),
            'date'      => __('Transaction date', 'woocommerce-nestpay'),
            'status'    => __('Transaction status', 'woocommerce-nestpay'),
            'response'  => __('Transaction response', 'woocommerce-nestpay'),
        ]);
    }

    public function addLoaderTemplate() : void {
        ?>
        <div id="nestpay-loading">
            <div class="img">
                <img src="<?= assets_uri('images/loading.svg'); ?>">
            </div>
        <?php
    }

    public function checkEnqueueing(bool $enqueue) : bool {


        return is_checkout_pay_page() && !is_user_logged_in();

    }

    public function enqueueHCaptcha() : void {

        if (!is_checkout_pay_page() || is_user_logged_in()) {
            return;
        }

        wp_enqueue_script('nestpay-hcaptcha', 'https://hCaptcha.com/1/api.js', [], '1.0', true);

    }

}
