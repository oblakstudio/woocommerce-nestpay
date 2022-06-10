<?php
/**
 * Nestpay Checkout form
 *
 * This template CANNOT be overridden by copying it to yourtheme/woocommerce/checkout/form-nestpay.php.
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage WooCommerce\Templates
 *
 * @version 1.0.0
 *
 * @var string $payment_url        Payment URL.
 * @var array  $transaction_fields Transaction fields array.
 * @var bool   $enable_hcaptcha    Enable hCaptcha.
 */

defined( 'ABSPATH' ) || exit;

$form_style = $auto_redirect ? 'none' : 'block';

?>
<form method="POST" action="<?php echo esc_url( $payment_url ); ?>" id="nestpay-payment-form" data-auto-redirect="<?php echo esc_attr( wc_bool_to_string( $auto_redirect ) ); ?>" style="display: <?php echo esc_attr( $form_style ); ?>;">
    <?php foreach ( $transaction_fields as $field_name => $field_value ) : ?>
        <input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>">
    <?php endforeach; ?>
    <?php if ( $enable_hcaptcha ) : ?>
        <div class="h-captcha" data-sitekey="7c4d6bd0-1b53-441e-8aad-5ac154d6fc5a"></div>
    <?php endif; ?>
    <input type="hidden" name="hcActive" value="<?php echo esc_attr( wc_bool_to_string( $enable_hcaptcha ) ); ?>">
    <input class="button button-proceed" type="submit" value="<?php esc_attr_e( 'Continue to payment', 'wc-serbian-nestpay' ); ?>">
</form>
