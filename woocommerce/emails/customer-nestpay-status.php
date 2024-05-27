<?php
/**
 * Customer transaction success email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-nestpay-status.php.
 *
 * HOWEVER, on occasion Oblak Solutions will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage WooCommerce\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Email header
 *
 * @hooked WC_Emails::email_header() Output the email header
 */
// Documented in lib/WooCommerce/Emails.php.
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>

<p>
    <?php
    if ( $order->is_paid() ) {
        esc_html_e( 'The order amount has been successfully reserved on your payment card.', 'wc-serbian-nestpay' );
        echo ' ' . wc_price( $order->get_total() ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    } else {
        esc_html_e( 'Transaction failed. Your payment card is not charged.', 'wc-serbian-nestpay' );
    }
    ?>
</p>

<?php
/**
 * Transaction details
 *
 * @hooked WooCommerce_Nestpay::email_transaction_details() Shows the transaction details table.
 * @since 2.0.0
 */
// Documented in lib/WooCommerce/Emails.php.
do_action( 'woocommerce_email_nestpay_transaction_details', $order, $transaction, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
    echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/**
 * Email footer
 *
 * @hooked WC_Emails::email_footer() Output the email footer
 * @since 2.0.0
 */
// Documented in lib/WooCommerce/Emails.php.
do_action( 'woocommerce_email_footer', $email );
