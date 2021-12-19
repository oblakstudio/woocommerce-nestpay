<?php
/**
 * WooCommerce NPG installer
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

namespace Oblak\NPG\Utils;

use WC_Admin_Notices;

/**
 * Installer class
 */
class Installer {
    /**
     * Init function for the installer
     */
    public static function init() {
        add_action( 'init', [__CLASS__, 'checkVersion'] );
        add_action( 'plugin_action_links_' . WCNPG_PLUGIN_BASENAME, [__CLASS__, 'plugin_action_links'] );
    }

    /**
     * Check if we're running latest version
     */
    public static function checkVersion() {
        if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'wcnpg_version', '0.0.1' ), WCNPG()->version, '<' ) ) {
            self::install();
            do_action( 'wcnpg_updated' );
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

        self::createTables();
        self::verifyBaseTables();
        self::createOptions();
        self::update_wcnpg_version();

        delete_transient( 'wcnpg_installing' );
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
    private static function createTables() {
        global $wpdb;

        $wpdb->hide_errors();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta( self::getSchema() );
    }

    /**
     * Verifies if the database tables have been created.
     *
     * @param  boolean $modify_notice Can we modify the notice.
     * @param  boolean $execute       Are we executing table creation.
     */
    public static function verifyBaseTables( $modify_notice = true, $execute = false ) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        if ( $execute ) {
            self::createTables();
        }

        $queries        = dbDelta( self::getSchema(), false );
        $missing_tables = [];

        foreach ( $queries as $table_name => $result ) {
            if ( "Created table {$table_name}" === $result ) {
                $missing_tables[] = $table_name;
            }
        }

        if ( count( $missing_tables ) > 0 ) {
            if ( $modify_notice ) {
                WC_Admin_Notices::add_custom_notice( 'wcnpg_tables_missing', 'Database tables missing' );
            }
        } else {
            if ( $modify_notice ) {
                WC_Admin_Notices::remove_notice( 'wcnpg_tables_missing' );
            }
            update_option( 'wcnpg_schema_version', WCNPG()->db_version );
            delete_option( 'wcpng_schema_missing_tables' );
        }
    }

    /**
     * Get table schema
     *
     * See: https://github.com/oblakstudio/woocommerce-nestpay/wiki/Database-Schema
     */
    private static function getSchema() {
        global $wpdb;

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        $tables =
        "CREATE TABLE {$wpdb->prefix}woocommerce_npp_transactions (
            ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            processed TINYINT( 4 ) NOT null default 0,
            oid VARCHAR( 64 ) NOT null,
            trantype VARCHAR( 20 ) NOT null,
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
            email VARCHAR( 64 ) default null,
            tel VARCHAR( 32 ) default null,
            description VARCHAR( 255 ) default null,
            BillToCompany VARCHAR( 255 ) default null,
            BillToName VARCHAR( 255 ) default null,
            BillToStreet1 VARCHAR( 255 ) default null,
            BillToStreet2 VARCHAR( 255 ) default null,
            BillToCity VARCHAR( 64 ) default null,
            BillToStateProv VARCHAR( 32 ) default null,
            BillToPostalCode VARCHAR( 32 ) default null,
            BillToCountry VARCHAR( 32 ) default null,
            ShipToCompany VARCHAR( 255 ) default null,
            ShipToName VARCHAR( 255 ) default null,
            ShipToStreet1 VARCHAR( 255 ) default null,
            ShipToStreet2 VARCHAR( 255 ) default null,
            ShipToCity VARCHAR( 64 ) default null,
            ShipToStateProv VARCHAR( 32 ) default null,
            ShipToPostalCode VARCHAR( 32 ) default null,
            ShipToCountry VARCHAR( 32 ) default null,
            DimCriteria1 VARCHAR( 64 ) default null,
            DimCriteria2 VARCHAR( 64 ) default null,
            DimCriteria3 VARCHAR( 64 ) default null,
            DimCriteria4 VARCHAR( 64 ) default null,
            DimCriteria5 VARCHAR( 64 ) default null,
            DimCriteria6 VARCHAR( 64 ) default null,
            DimCriteria7 VARCHAR( 64 ) default null,
            DimCriteria8 VARCHAR( 64 ) default null,
            DimCriteria9 VARCHAR( 64 ) default null,
            DimCriteria10 VARCHAR( 64 ) default null,
            comments VARCHAR( 255 ) default null,
            instalment VARCHAR( 3 ) null,
            INVOICENUMBER VARCHAR( 255 ) default null,
            storetype VARCHAR( 16 ) default null,
            lang VARCHAR( 16 ) default null,
            xid VARCHAR( 255 ) default null,
            HostRefNum VARCHAR( 255 ) default null,
            ReturnOid VARCHAR( 64 ) default null,
            MaskedPan VARCHAR( 20 ) default null,
            rnd VARCHAR( 20 ) default null,
            merchantID VARCHAR( 255 ) default null,
            txstatus VARCHAR( 255 ) default null,
            iReqCode VARCHAR( 255 ) default null,
            iReqDetail VARCHAR( 255 ) default null,
            vendorCode VARCHAR( 255 ) default null,
            PAResSyntaxOK VARCHAR( 255 ) default null,
            PAResVerified VARCHAR( 255 ) default null,
            eci VARCHAR( 255 ) default null,
            cavv VARCHAR( 255 ) default null,
            cavvAlgorthm VARCHAR( 255 ) default null,
            md VARCHAR( 255 ) default null,
            Version VARCHAR( 255 ) default null,
            sID VARCHAR( 255 ) default null,
            mdErrorMsg text default null,
            clientid VARCHAR( 255 ) default null,
            EXTRA_TRXDATE VARCHAR( 255 ) default null,
            ACQBIN VARCHAR( 255 ) default null,
            acqStan VARCHAR( 255 ) default null,
            cavvAlgorithm VARCHAR( 255 ) default null,
            digest VARCHAR( 255 ) default null,
            dsId VARCHAR( 255 ) default null,
            Ecom_Payment_Card_ExpDate_Month VARCHAR( 255 ) default null,
            Ecom_Payment_Card_ExpDate_Year VARCHAR( 255 ) default null,
            EXTRA_CARDBRAND VARCHAR( 255 ) default null,
            EXTRA_CARDISSUER VARCHAR( 255 ) default null,
            EXTRA_INVOICENUMBER VARCHAR( 255 ) default null,
            failUrl VARCHAR( 255 ) default null,
            HASH VARCHAR( 255 ) default null,
            hashAlgorithm VARCHAR( 255 ) default null,
            HASHPARAMS VARCHAR( 255 ) default null,
            HASHPARAMSVAL VARCHAR( 255 ) default null,
            okurl VARCHAR( 255 ) default null,
            refreshtime VARCHAR( 255 ) default null,
            SettleId VARCHAR( 255 ) default null,
            created_at timestamp NOT null default CURRENT_TIMESTAMP,
            updated_at timestamp NOT null default CURRENT_TIMESTAMP,
            PRIMARY KEY  (ID),
            KEY processed (processed),
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
    public static function createOptions() {
        // add_option('woocommerce_serbian', [
        // 'enabled_customer_type'  => 'both',
        // 'remove_unneeded_fields' => 'yes',
        // 'fix_currency_symbol'    => 'no',
        // ]).
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
        $action_links = [
            'settings' => sprintf(
                '<a href="%s" aria-label="%s">%s</a>',
                admin_url( 'admin.php?page=wc-settings&tab=checkout&section=nestpay' ),
                esc_attr__( 'Plugin Settings', 'woocommerce-nestpay' ),
                esc_html__( 'Plugin Settings', 'woocommerce-nestpay' ),
            ),
        ];

        return array_merge( $action_links, $links );
    }
}
