<?php
/**
 * WooCommerce NPG installer
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 2.0.0
 */

namespace Oblak\NPG\Core;

use Oblak\WP\Base_Plugin_Installer;

/**
 * Installer class
 */
class Installer extends Base_Plugin_Installer {

    /**
     * {@inheritDoc}
     */
    protected function set_defaults() {
        $this->version = WCNPG_VERSION;
        $this->slug    = 'woocommerce-nestpay';
        $this->name    = __( 'WooCommerce NestPay Payment Gateway', 'wc-serbian-nestpay' );
    }


    /**
     * Get table schema
     *
     * See: https://github.com/oblakstudio/woocommerce-nestpay/wiki/Database-Schema
     */
    protected function get_schema(): ?string {
        global $wpdb;

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        $tables = <<<SQL
        CREATE TABLE {$wpdb->prefix}woocommerce_npp_transactions (
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
        ) {$collate};
        SQL;

        return $tables;
    }

    /**
     * Show action links on the plugin screen
     *
     * @param  mixed $links Plugin Action links.
     * @return array
     */
    public function plugin_action_links( $links ) {
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
