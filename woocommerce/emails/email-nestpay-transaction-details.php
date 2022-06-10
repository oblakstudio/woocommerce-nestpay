<?php
/**
 * Customer transaction success email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-nestpay-transaction-details.php.
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
 *
 * @version 2.0.0
 *
 * @var WC_Order $order  Order object.
 * @var array    $fields Transaction fields array.
 */

defined( 'ABSPATH' ) || exit;

$text_align = is_rtl() ? 'right' : 'left';

/**
 * Type overrides
 */

?>
<div style="margin-bottom: 40px;">
    <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
        <tbody>
            <tr>
                <td style="text-align: <?php echo esc_attr( $text_align ); ?>;">
                    <?php esc_html_e( 'Order ID', 'wc-serbian-nestpay' ); ?>
                </td>
                <td style="text-align: <?php echo esc_attr( $text_align ); ?>;">
                    <?php echo esc_html( $order->get_order_number() ); ?>
                </td>
            </tr>
        <?php foreach ( $fields as $label => $value ) : ?>
            <tr>
                <td style="text-align: <?php echo esc_attr( $text_align ); ?>; vertical-align: top; padding-right: 10px;">
                    <?php echo esc_html( $label ); ?>
                </td>
                <td style="text-align: <?php echo esc_attr( $text_align ); ?>; vertical-align: top;">
                    <?php echo esc_html( $value ); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>

    <?php
    /**
     * Add custom content after transaction details table
     *
     * @since 1.0.0
     * @param WC_Order $order  Order object
     * @param array    $fields Transaction field key-value pairs
     */
    do_action( 'woocommerce_email_after_nestpay_transaction_details', $order, $transaction, $fields );
    ?>

</div>
