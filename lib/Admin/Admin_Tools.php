<?php
/**
 * Admin_Tools class file
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage Admin
 */

namespace Oblak\NPG\Admin;

use Oblak\NPG\Utils\Installer;

/**
 * Adds and runs admin tools
 */
class Admin_Tools {

    /**
     * Class Constructor
     */
    public function __construct() {
        add_filter( 'woocommerce_debug_tools', array( $this, 'add_debug_tools' ), 99, 1 );
    }

    /**
     * Adds plugin debug tools to WooCommerce debug tools
     *
     * @param  array $tools List of debug tools.
     * @return array        Modified list of debug tools.
     */
    public function add_debug_tools( $tools ) {
        $nestpay_tools = array(
            'nestpay_verify_db_tables' => array(
                'name'     => sprintf(
                    '%s: %s',
                    __( 'WooCommerce NestPay Payment Gateway', 'wc-serbian-nestpay' ),
                    __( 'Verify base database tables', 'woocommerce' )
                ),
                'button'   => __( 'Verify database', 'woocommerce' ),
                'desc'     => __( 'Verify if all base database tables are present.', 'woocommerce' ),
                'callback' => array( $this, 'verify_db_tables' ),
            ),
        );

        return array_merge( $tools, $nestpay_tools );
    }

    /**
     * Verifies if all base database tables are present
     *
     * @return string|false Success mesage if all database tables are present, false otherwise
     */
    public function verify_db_tables() {
        WCNPG()->amn->remove_notice( 'wcnpg_missing_tables', true );

        $missing_tables = Installer::verify_base_tables( true, true );

        return 0 === count( $missing_tables )
            ? __( 'Database verified successfully.', 'woocommerce' )
            : __( 'Verifying database... One or more tables are still missing: ', 'woocommerce' ) . implode( ', ', $missing_tables );
    }
}
