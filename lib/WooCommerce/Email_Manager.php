<?php
/**
 * Email_Manager class file.
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage WooCommerce
 */

namespace Oblak\NPG\WooCommerce;

use Oblak\NPG\WooCommerce\Email\Transaction_Failure_Email;
use Oblak\NPG\WooCommerce\Email\Transaction_Success_Email;
use Oblak\NPG\WooCommerce\Data\Nestpay_Transaction;
use WC_Order;

/**
 * Registers email classes and handles template overrides
 */
class Email_Manager {

    /**
     * Email templates we want to override
     *
     * @var array
     */
    private static $templates = array(
        'emails/customer-nestpay-status.php',
        'emails/email-nestpay-transaction-details.php',
    );

    /**
     * Class Constructor
     */
    public function __construct() {
        add_filter( 'woocommerce_get_path_define_tokens', array($this, 'add_path_define_token'), 50, 1 );
        add_filter( 'woocommerce_locate_template', array($this, 'override_templates'), 50, 2 );
        add_filter( 'woocommerce_email_classes', array($this, 'add_email_classes'), 99, 1 );

        add_action( 'woocommerce_email_nestpay_transaction_details', array($this, 'transaction_details'), 99, 2 );
    }

    /**
     * Adds plugin path to list of tokens.
     *
     * @param  array $tokens List of tokens.
     * @return array         Modified list of tokens.
     */
    public function add_path_define_token( $tokens ) {
        $tokens['WCNPG_PATH'] = WCNPG_PLUGIN_PATH;
        return $tokens;
    }

    /**
     * Overrides template directory for plugin templates.
     *
     * @param  string $template      Template path.
     * @param  string $template_name Template name.
     * @return string                Modified template path.
     */
    public function override_templates( $template, $template_name ) {
        // If not one of our templates, bail out immediately.
        if ( ! in_array( $template_name, self::$templates, true ) ) {
            return $template;
        }

        // Set default searchable path to woocommerce.
        $default_path = WC()->template_path();

        // Try to locate the template file in the theme.
        $template = locate_template(array(
            trailingslashit( $default_path ) . $template_name,
            $template_name,
        ));

        if ( ! empty( $template ) ) {
            return $template;
        }

        return WCNPG_PLUGIN_PATH . 'woocommerce/' . $template_name;
    }

    /**
     * Adds our emails to the list of WooCommerce emails.
     *
     * @param  array $classes List of WooCommerce email classes.
     * @return array          Modified list of WooCommerce email classes.
     */
    public function add_email_classes( $classes ) {
        $classes['WC_Email_NestPay_Success'] = new Transaction_Success_Email();
        $classes['WC_Email_NestPay_Failure'] = new Transaction_Failure_Email();

        return $classes;
    }

    /**
     * Adds transaction details to the status emails.
     *
     * @param  WC_Order            $order       Order object.
     * @param  Nestpay_Transaction $transaction Transaction object.
     */
    public function transaction_details( $order, $transaction ) {
        $data = array();

        foreach ( wcnpg_get_user_transaction_fields() as $prop => $label ) {
            if ( method_exists( $transaction, "get_{$prop}" ) ) {
                $data[ $label ] = $transaction->{"get_{$prop}"}();
            } else {
                $data[ $label ] = apply_filters( 'woocommerce_nestpay_transaction_field_prop', '', $transaction, $order );
            }
        };

        wc_get_template(
            'emails/email-nestpay-transaction-details.php',
            array(
                'fields'      => $data,
                'order'       => $order,
                'transaction' => $transaction,
            )
        );
    }

}
