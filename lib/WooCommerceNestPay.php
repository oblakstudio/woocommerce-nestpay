<?php
/**
 * WooCommerce NPG setup
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

namespace Oblak\NPG;

use Oblak\NPG\Utils\Installer;

/**
 * Main WCNPG class
 */
final class WooCommerceNestPay {
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
     * Plugin instance
     *
     * @var WooCommerceNestPay
     */
    protected static $instance = null;

    /**
     * Plugin settings
     *
     * @var array
     */
    protected $options = [];

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
    public static function getInstance() {
        return is_null( self::$instance )
        ? self::$instance = new self()
        : self::$instance;
    }

    /**
     * Private class constructor
     */
    private function __construct() {
        $this->defineConstants();
        $this->defineTables();
        $this->loadClasses();
        $this->initHooks();
    }

    /**
     * Add needed constants
     */
    private function defineConstants() {
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
    private function defineTables() {
        global $wpdb;

        $tables = [
            'npp_transactions' => 'woocommerce_npp_transactions',
        ];

        foreach ( $tables as $name => $table ) {
            $wpdb->$name    = $wpdb->prefix . $table;
            $wpdb->tables[] = $table;
        }
    }

    /**
     * Load plugin classes
     */
    private function loadClasses() {
        Installer::init();
    }

    /**
     * Initialize our plugin
     */
    private function initHooks() {

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
}
