<?php
/**
 * Customer transaction success email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-on-hold-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$text_align = is_rtl() ? 'right' : 'left';

$fields = [
    'Response'         => __('Transaction status', 'woocommerce-nestpay'),
    'TransId'          => __('Transaction ID', 'woocommerce-nestpay'),
    'ProcReturnCode'   => __('Status code', 'woocommerce-nestpay'), 
    'AuthCode'         => __('Authorization code', 'woocommerce-nestpay'),
    'mdStatus'         => __('3D Status', 'woocommerce-nestpay'),
    'maskedCreditCard' => __('Payment card number', 'woocommerce-nestpay'),
    // 'instalment'       => __('Installments', 'woocommerce-nestpay'),
];

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<?php if ($order->is_paid()) : ?>
    <p><?= __('The order amount has been successfully reserved on your payment card.', 'woocommerce-nestpay') . ' ' . wc_price($order->get_total()); ?></p>
<?php else : ?>
    <p><?= __('Transaction failed. Your payment card is not charged.', 'woocommerce-nestpay'); ?>
<?php endif; ?>


<div style="margin-bottom: 40px;">
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
		<!-- <thead>
            <tr>
                <th>
                </th>
                <th>

                </th>
            </tr>
        </thead> -->
        <tbody>
            <tr>
                <td style="text-align: <?php echo $text_align; ?>;">
                    <?= __('Order ID', 'woocomerce-nestpay'); ?>
                </td>
                <td style="text-align: <?php echo $text_align; ?>;">
                    <?= $order->get_id(); ?>
                </td>
            </tr>
        <?php foreach ($fields as $key => $label) : ?>
            <tr>
                <td style="text-align: <?php echo $text_align; ?>; vertical-align: top; padding-right: 10px;"><?php echo $label; ?></td>
                <td style="text-align: <?php echo $text_align; ?>; vertical-align: top;"><?= $response->$key; ?></td>
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
    do_action('woocommerce_nestpay_transaction_email', $order, $fields);
    ?>

</div>

<?php

if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
