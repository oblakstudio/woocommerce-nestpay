<?php
/**
 * WooCommerce NPG setup
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

namespace Oblak\NPG;

use Oblak\Admin_Notice_Manager;
use Oblak\NPG\Utils\Installer;
use Oblak\NPG\WooCommerce\Gateway\Nestpay_Client;

/**
 * Main WCNPG class
 */
final class Woocommerce_Nestpay {
    /**
     * Plugin Version
     *
     * @var string
     */
    public $version = '2.0.0';

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
     * Plugin instance
     *
     * @var Woocommerce_Nestpay
     */
    protected static $instance = null;

    /**
     * Plugin settings
     *
     * @var array
     */
    protected $options = array();

    /**
     * Disallow cloning
     */
    public function __clone() {
        wc_doing_it_wrong( __FUNCTION__, 'Cloning is disabled', 'NestPay 2.0.0' );
    }

    /**
     * Disallow serialization
     */
    public function __wakeup() {
        wc_doing_it_wrong( __FUNCTION__, 'Unserializing is disabled', 'NestPay 2.0.0' );
    }

    /**
     * Retrieves the singleton instance
     *
     * @return WooCommerceSerbian
     */
    public static function get_instance() {
        return is_null( self::$instance )
        ? self::$instance = new self()
        : self::$instance;
    }

    /**
     * Private class constructor
     */
    private function __construct() {
        $this->define_constants();
        $this->define_tables();
        $this->load_classes();
        $this->init_hooks();
    }

    /**
     * Add needed constants
     */
    private function define_constants() {
        $this->define( 'WCNPG_ABSPATH', dirname( WCNPG_PLUGIN_FILE ) . '/' );
        $this->define( 'WCNPG_PLUGIN_BASENAME', plugin_basename( WCNPG_PLUGIN_FILE ) );
        $this->define( 'WCNPG_PLUGIN_PATH', plugin_dir_path( WCNPG_PLUGIN_FILE ) );
        $this->define( 'WCNPG_VERSION', $this->version );
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * Adds plugin tables to WPDB class
     */
    private function define_tables() {
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
     * Load plugin classes
     */
    private function load_classes() {
        Installer::init();
        $this->client = new Nestpay_Client();
        $this->amn    = Admin_Notice_Manager::get_instance();
    }

    /**
     * Initialize our plugin
     */
    private function init_hooks() {
        add_filter( 'woocommerce_data_stores', array($this, 'init_data_store'), 50, 1 );
        add_filter( 'woocommerce_payment_gateways', array($this, 'add_payment_gateway'), 50, 1 );

        add_filter( 'admin_init', array($this, 'admin_init'), 99 );
        add_filter( 'woocommerce_init', array($this, 'woocommerce_init'), 99 );
        add_filter( 'init', array($this, 'init'), 99 );
    }

    /**
     * Adds the transaction data store to WooCommerce data store list
     *
     * @param  string[] $data_stores List of data stores.
     * @return string[]              List of modified data stores.
     */
    public function init_data_store( $data_stores ) {
        $data_stores['nestpay-transaction'] = 'Oblak\\NPG\\WooCommerce\\Data\\Nestpay_Transaction_Data_Store';
        return $data_stores;
    }

    /**
     * Adds our Payment Gateway to list of WooCommerce Gateways
     *
     * @param  string[] $gateways List of gateways.
     * @return string[]           Modified list of gateways.
     */
    public function add_payment_gateway( $gateways ) {
        $gateways[] = '\\Oblak\NPG\\WooCommerce\\Gateway\\Nestpay_Gateway';
        return $gateways;
    }

    /**
     * Initialize Admin
     */
    public function admin_init() {
        new Admin\AdminAssets();
        new Admin\Admin_Tools();
    }

    /**
     * Initialize plugin when WordPress Initialises.
     */
    public function init() {
        $this->load_textdomain();
    }

    /**
     * Initializes Woocommerce addons and hooks
     */
    public function woocommerce_init() {
        new WooCommerce\Email_Manager();
        new WooCommerce\Order\Admin_Order_Columns();
        new WooCommerce\Order\Order_Actions();

    }

    /**
     * Loads the plugin textdomain
     */
    private function load_textdomain() {
        load_plugin_textdomain(
            'woocommerce-nestpay',
            false,
            dirname( WCNPG_PLUGIN_BASENAME ) . '/languages'
        );
    }

    /**
     * What type of request is this?
     *
     * Copied verbatim from WooCommerce
     *
     * @param  string $type admin, ajax, cron or frontend.
     * @return bool
     */
    public function is_request( $type ) {
        switch ( $type ) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined( 'DOING_AJAX' );
            case 'cron':
                return defined( 'DOING_CRON' );
            case 'frontend':
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! WC()->is_rest_api_request();
        }
    }

    /**
     * Get the plugin URL
     *
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', WCNPG_PLUGIN_FILE ) );

    }
}
