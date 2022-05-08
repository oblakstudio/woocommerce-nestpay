<?php
/**
 * Nestpay Checkout form
 *
 * This template CANNOT be overridden by copying it to yourtheme/woocommerce/checkout/form-nestpay.php.
 *
 * @package WooCommerce NestPay Payment Gateway
 * @subpackage WooCommerce\Templates
 *
 * @version 2.0.0
 *
 * @var string $payment_url        Payment URL.
 * @var array  $transaction_fields Transaction fields array.
 * @var bool   $enable_hcaptcha    Enable hCaptcha.
 * @var string $hcaptcha_site_key  hCaptcha site key.
 */

defined( 'ABSPATH' ) || exit;

?>
<form method="POST" action="<?php echo esc_url( $payment_url ); ?>" id="nestpay-payment-form">
    <?php foreach ( $transaction_fields as $field_name => $field_value ) : ?>
        <input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>">
    <?php endforeach; ?>
    <?php if ( $enable_hcaptcha ) : ?>
        <div class="h-captcha" data-sitekey="<?php echo esc_attr( $hcaptcha_site_key ); ?>"></div>
    <?php endif; ?>
    <input type="hidden" name="hcActive" value="<?php echo esc_attr( wc_bool_to_string( $enable_hcaptcha ) ); ?>">
    <input class="button button-proceed" type="submit" value="<?php esc_attr_e( 'Continue to payment', 'woocommerce-nestpay' ); ?>">
</form>
