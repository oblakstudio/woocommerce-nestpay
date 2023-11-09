<?php
/**
 * WooCommerce NPG installer
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

namespace Oblak\NPG\Utils;

/**
 * Installer class
 */
class Installer {
    /**
     * Init function for the installer
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'check_version' ) );
        add_action( 'plugin_action_links_' . WCNPG_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
    }

    /**
     * Check if we're running latest version
     */
    public static function check_version() {
        if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'wcnpg_version', '0.0.1' ), WCNPG()->version, '<' ) ) {
            self::install();
            do_action( 'wcnpg_updated' ); //phpcs:ignore
        }
    }

    /**
     * Install the plugin
     */
    public static function install() {
        if ( ! is_blog_installed() ) {
            return;
        }
        if ( get_transient( 'wcnpg_installing' ) === 'yes' ) {
            return;
        }

        set_transient( 'wcnpg_installing', 'yes', MINUTE_IN_SECONDS * 5 );
        wc_maybe_define_constant( 'WCNPG_INSTALLING', true );

        self::create_tables();
        self::verify_base_tables();
        self::create_options();
        self::update_wcnpg_version();

        delete_transient( 'wcnpg_installing' );

        /**
         * Fires after the plugin has been installed.
         *
         * @since 2.0.0
         */
        do_action( 'wcnpg_installed' );
    }

    /**
     * Sets up the database tables which the plugin needs to function.
     * WARNING: If you're fucking around with this method, make sure that it's safe to call regardless of the state of the database.
     *
     * This is called from install method above and runs only when installing or updating the plugin.
     * Optionally, can be called from the tools section of WooCommerce
     *
     * Tables:
     *  * woocommerce_npp_transactions - Table for storing all transactions done via NestPay
     *
     * @since 2.0.0
     */
    private static function create_tables() {
        global $wpdb;

        $wpdb->hide_errors();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta( self::get_schema() );
    }

    /**
     * Verifies if the database tables have been created.
     *
     * @param  bool $modify_notice Can we modify the notice.
     * @param  bool $execute       Are we executing table creation.
     * @return string[]            List of missing tables.
     */
    public static function verify_base_tables( $modify_notice = true, $execute = false ) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        if ( $execute ) {
            self::create_tables();
        }

        $queries        = dbDelta( self::get_schema(), false );
        $missing_tables = array();

        foreach ( $queries as $table_name => $result ) {
            if ( "Created table {$table_name}" === $result ) {
                $missing_tables[] = $table_name;
            }
        }

        if ( count( $missing_tables ) > 0 ) {
            if ( $modify_notice ) {
                WCNPG()->amn->add_notice(
                    'missing_tables',
                    array(
						'type'        => 'error',
						'caps'        => 'manage_woocommerce',
						'message'     => sprintf(
							'<p><strong>%s</strong> - %s: %s</p>',
							esc_html__( 'WooCommerce NestPay Payment Gateway', 'wc-serbian-nestpay' ),
							esc_html__( 'The following tables are missing: ', 'wc-serbian-nestpay' ),
							implode( ', ', $missing_tables ),
						),
						'dismissible' => false,
						'persistent'  => true,
                    ),
                    'wcnpg',
                    true
                );
            }
        } else {
            if ( $modify_notice ) {
                WCNPG()->amn->remove_notice( 'wcnpg_missing_tables', true );
            }
            update_option( 'wcnpg_schema_version', WCNPG()->db_version );
            delete_option( 'wcpng_schema_missing_tables' );
        }

        return $missing_tables;
    }

    /**
     * Get table schema
     *
     * See: https://github.com/oblakstudio/woocommerce-nestpay/wiki/Database-Schema
     */
    private static function get_schema() {
        global $wpdb;

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        $tables =
        "CREATE TABLE {$wpdb->prefix}woocommerce_npp_transactions (
            ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id BIGINT UNSIGNED NOT NULL,
            oid VARCHAR( 64 ) NOT null,
            trantype VARCHAR( 20 ) NOT null,
            protocol VARCHAR( 20 ) NOT null,
            amount DECIMAL( 12, 2 ) NOT null,
            currency VARCHAR( 3 ) NOT null,
            Response VARCHAR( 10 ) default null,
            ProcReturnCode VARCHAR( 2 ) default null,
            mdStatus VARCHAR( 3 ) default null,
            ErrMsg VARCHAR( 255 ) default null,
            AuthCode VARCHAR( 32 ) default null,
            TransId VARCHAR( 64 ) default null,
            TRANID VARCHAR( 64 ) default null,
            clientIp VARCHAR( 15 ) default null,
            payResults_dsId VARCHAR( 64 ) default null,
            signature VARCHAR( 1024 ) default null,
            description VARCHAR( 255 ) default null,
            orgHash VARCHAR( 512 ) default null,
            HASHPARAMS VARCHAR( 512 ) default null,
            HASH VARCHAR( 512 ) default null,
            HASHPARAMSVAL VARCHAR( 512 ) default null,
            orgRnd VARCHAR( 64 ) default null,
            comments VARCHAR( 255 ) default null,
            instalment VARCHAR( 3 ) null,
            storetype VARCHAR( 16 ) default null,
            lang VARCHAR( 16 ) default null,
            xid VARCHAR( 255 ) default null,
            HostRefNum VARCHAR( 255 ) default null,
            ReturnOid VARCHAR( 64 ) default null,
            MaskedPan VARCHAR( 20 ) default null,
            rnd VARCHAR( 20 ) default null,
            merchantID VARCHAR( 255 ) default null,
            hc_active SMALLINT( 1 ) default null,
            txstatus VARCHAR( 255 ) default null,
            iReqCode VARCHAR( 255 ) default null,
            iReqDetail VARCHAR( 255 ) default null,
            vendorCode VARCHAR( 255 ) default null,
            PAResSyntaxOK VARCHAR( 255 ) default null,
            PAResVerified VARCHAR( 255 ) default null,
            veresEnrolledStatus VARCHAR( 255 ) default null,
            eci VARCHAR( 255 ) default null,
            cavv VARCHAR( 255 ) default null,
            cavvAlgorthm VARCHAR( 255 ) default null,
            md VARCHAR( 255 ) default null,
            Version VARCHAR( 255 ) default null,
            sID VARCHAR( 255 ) default null,
            mdErrorMsg text default null,
            clientid VARCHAR( 255 ) default null,
            EXTRA_TRXDATE VARCHAR( 255 ) default null,
            EXTRA_UCAFINDICATOR VARCHAR( 255 ) default null,
            ACQBIN VARCHAR( 255 ) default null,
            acqStan VARCHAR( 255 ) default null,
            cavvAlgorithm VARCHAR( 255 ) default null,
            digest VARCHAR( 255 ) default null,
            dsId VARCHAR( 255 ) default null,
            isHPPCall VARCHAR( 255 ) default null,
            Ecom_Payment_Card_ExpDate_Month VARCHAR( 255 ) default null,
            Ecom_Payment_Card_ExpDate_Year VARCHAR( 255 ) default null,
            EXTRA_CARDBRAND VARCHAR( 255 ) default null,
            EXTRA_CARDISSUER VARCHAR( 255 ) default null,
            shopurl VARCHAR( 255 ) default null,
            callbackCall VARCHAR( 20 ) default null,
            encoding VARCHAR( 20 ) default null,
            refreshtime VARCHAR( 255 ) default null,
            SettleId VARCHAR( 255 ) default null,
            created_at timestamp NOT null default CURRENT_TIMESTAMP,
            updated_at timestamp NOT null default CURRENT_TIMESTAMP,
            _charset_ VARCHAR( 10 ) default null,
            PRIMARY KEY  (ID),
            KEY trantype (trantype),
            KEY currency (currency),
            KEY Response (Response),
            KEY ProcReturnCode (ProcReturnCode),
            KEY mdStatus (mdStatus),
            KEY AuthCode (AuthCode)
        ) {$collate};";

        return $tables;
    }

    /**
     * Adds default plugin options
     *
     * @since 2.0.0
     */
    public static function create_options() {
    }

    /**
     * Update wcnpg version to current.
     */
    public static function update_wcnpg_version() {
        update_option( 'wcnpg_version', WCNPG()->version );
    }

    /**
     * Show action links on the plugin screen
     *
     * @param  mixed $links Plugin Action links.
     * @return array
     */
    public static function plugin_action_links( $links ) {
        $action_links = array(
            'settings' => sprintf(
                '<a href="%s" aria-label="%s">%s</a>',
                admin_url( 'admin.php?page=wc-settings&tab=checkout&section=nestpay' ),
                esc_attr__( 'Plugin Settings', 'wc-serbian-nestpay' ),
                esc_html__( 'Plugin Settings', 'wc-serbian-nestpay' ),
            ),
        );

        return array_merge( $action_links, $links );
    }
}
