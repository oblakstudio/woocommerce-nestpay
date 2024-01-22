<?php //phpcs:disable Squiz.Commenting.VariableComment.MissingVar
/**
 * WooCommerce NPG setup
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

namespace Oblak\NPG;

use Oblak\NPG\Core\Installer;
use Oblak\NPG\WooCommerce\Data\Nestpay_Transaction;
use Oblak\NPG\WooCommerce\Gateway\Nestpay_Client;
use Oblak\WP\Admin_Notice_Manager;
use Oblak\WP\Loader_Trait;
use Oblak\WP\Traits\Hook_Processor_Trait;
use Oblak\WP\Traits\Singleton_Trait;

/**
 * Main WCNPG class
 */
final class WooCommerce_Nestpay {
    use Hook_Processor_Trait;
    use Loader_Trait;
    use Singleton_Trait;

    /**
     * Plugin Version
     *
     * @var string
     */
    public $version = '1.1.3';

    /**
     * DB Version
     *
     * @var string
     */
    public $db_version = '200';

    /**
     * NestPay client
     *
     * @var Nestpay_Client
     */
    public $client = null;

    /**
     * Admin notice manager
     *
     * @var Admin_Notice_Manager
     */
    public $amn = null;

    /**
     * Plugin settings
     *
     * @var array
     */
    protected $options = array();

    /**
     * Transaction factory
     *
     * @var WooCommerce\Data\NestPay_Transaction_Factory
     */
    public WooCommerce\Data\NestPay_Transaction_Factory $transaction_factory;

    /**
     * Class Constructor
     */
    protected function __construct() {
        $this->load_classes();
        $this->init( 'woocommerce_loaded', 1 );
    }

    /**
     * {@inheritDoc}
     */
    protected function get_dependencies(): array {
        return array(
            WooCommerce\Admin\Admin_Assets::class,
            WooCommerce\Admin\Image_Select_Field::class,
            WooCommerce\Admin\Order_Page_Addons::class,
            WooCommerce\Data\NestPay_Transaction_Factory::class,
            WooCommerce\Core\Template_Extender::class,
        );
    }

    /**
     * Load plugin classes
     */
    protected function load_classes() {
        Installer::instance()->init();
        $this->namespace = 'wc-serbian-nestpay';

        $this->init_asset_loader( include WCNPG_ABSPATH . 'config/assets.php' );
    }

    /**
     * Adds plugin tables to WPDB class
     *
     * @hook     plugins_loaded
     * @type     action
     * @priority PHP_INT_MAX
     */
    public function define_tables() {
        global $wpdb;

        $tables = array(
            'npp_transactions' => 'woocommerce_npp_transactions',
        );

        foreach ( $tables as $name => $table ) {
            $wpdb->$name    = $wpdb->prefix . $table;
            $wpdb->tables[] = $table;
        }
    }

    /**
     * Loads the plugin textdomain
     *
     * @hook     plugins_loaded
     * @type     action
     * @priority 1000
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'wc-serbian-nestpay',
            false,
            dirname( WCNPG_PLUGIN_BASENAME ) . '/languages'
        );
    }

    /**
     * Declares compatibility with HPOS
     *
     * @hook     before_woocommerce_init
     * @type     action
     * @priority 999
     */
    public function declare_hpos_compat() {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WCNPG_PLUGIN_FILE, true );
    }

    /**
     * Adds the transaction data store to WooCommerce data store list
     *
     * @param  string[] $data_stores List of data stores.
     * @return string[]              List of modified data stores.
     *
     * @hook     woocommerce_data_stores
     * @type     filter
     * @priority 50
     */
    public function init_data_store( $data_stores ) {
        return array_merge(
            $data_stores,
            array(
                'nestpay-transaction' => WooCommerce\Data\Nestpay_Transaction_Data_Store::class,
            )
        );
    }

    /**
     * Adds our Payment Gateway to list of WooCommerce Gateways
     *
     * @param  string[] $gateways List of gateways.
     * @return string[]           Modified list of gateways.
     *
     * @hook     woocommerce_payment_gateways
     * @type     filter
     * @priority 50
     */
    public function add_payment_gateway( $gateways ) {
        $gateways[] = WooCommerce\Gateway\Nestpay_Gateway::class;
        return $gateways;
    }

    /**
     * Adds our emails to the list of WooCommerce emails.
     *
     * @param  array $classes List of WooCommerce email classes.
     * @return array          Modified list of WooCommerce email classes.
     *
     * @hook woocommerce_email_classes
     * @type filter
     */
    public function add_email_classes( $classes ) {
        $classes['WC_Email_NestPay_Status'] = new WooCommerce\Email\Transaction_Status_Email();

        return $classes;
    }

    /**
     * Adds transaction details to the status emails.
     *
     * @param  WC_Order            $order       Order object.
     * @param  Nestpay_Transaction $transaction Transaction object.
     *
     * @hook woocommerce_email_nestpay_transaction_details
     * @type action
     */
    public function email_transaction_details( $order, $transaction ) {
        $data = array();

        foreach ( nestpay_get_transaction_fields() as $prop => $label ) {
            if ( method_exists( $transaction, "get_{$prop}" ) ) {
                $data[ $label ] = $transaction->{"get_{$prop}"}();
            } else {
                $data[ $label ] = apply_filters( 'woocommerce_nestpay_transaction_field_prop', '', $transaction, $order ); // phpcs:ignore
            }
        }

        wc_get_template(
            'emails/email-nestpay-transaction-details.php',
            array(
                'fields'      => $data,
                'order'       => $order,
                'transaction' => $transaction,
            )
        );
    }

    /**
     * Dequeues nestpay scripts on the frontend
     *
     * @param  bool $enqueue Whether to enqueue the script.
     * @return bool
     *
     * @hook wc-serbian-nestpay_load_scripts
     * @type filter
     */
    public function maybe_dequeue_script( bool $enqueue ): bool {
        if ( is_admin() ) {
            return $enqueue;
        }

        return is_checkout() && is_wc_endpoint_url( 'order-pay' );
    }

    /**
     * Get the plugin URL
     *
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', WCNPG_PLUGIN_FILE ) );
    }

    /**
     * Get Admin Notice Manager instance
     *
     * @return Admin_Notice_Manager
     */
    public function amn(): Admin_Notice_Manager {
        return Admin_Notice_Manager::get_instance();
    }
}
