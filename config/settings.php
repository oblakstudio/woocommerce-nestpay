<?php //phpcs:disable Generic.Files.LineLength.TooLong
/**
 * WooCommerce NPG settings
 *
 * @package WooCommerce NestPay Payment Gateway
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

return array(
    'enabled'           => array(
        'title'   => __( 'Enable/Disable', 'woocommerce-nestpay' ),
        'label'   => __( 'Enable NestPay', 'woocommerce-nestpay' ),
        'type'    => 'checkbox',
        'default' => 'no',
    ),
    'title'             => array(
        'title'       => __( 'Title', 'woocommerce-nestpay' ),
        'type'        => 'text',
        'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-nestpay' ),
        'default'     => 'NestPay',
        'desc_tip'    => true,
    ),
    'description'       => array(
        'title'       => __( 'Description', 'woocommerce-nestpay' ),
        'type'        => 'text',
        'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-nestpay' ),
        'default'     => __( 'Pay with your credit-card using NestPay', 'woocommerce-nestpay' ),
        'desc_tip'    => true,
    ),
    'advanced'          => array(
        'title'       => __( 'Advanced Settings', 'woocommerce-nestpay' ),
        'type'        => 'title',
        'description' => '',
    ),
    'testmode'          => array(
        'title'       => __( 'NestPay Sandbox', 'woocommerce-nestpay' ),
        'label'       => __( 'Enable NestPay Sandbox', 'woocommerce-nestpay' ),
        'type'        => 'checkbox',
        'description' => __( 'NestPay sandbox can be used to test payments', 'woocommerce-nestpay' ),
        'default'     => 'no',
    ),
    'debug'             => array(
        'title'       => __( 'Debug log', 'woocommerce-nestpay' ),
        'type'        => 'checkbox',
        'label'       => __( 'Enable logging', 'woocommerce-nestpay' ),
        'default'     => 'no',
        'description' => sprintf(
            // translators: %s log file path.
            __(
                'Log NestPay events, inside %s Note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.',
                'woocommerce-nestpay'
            ),
            '<code>' . WC_Log_Handler_File::get_log_file_path( 'nestpay' ) . '</code>'
        ),
    ),
    'auto_redirect'     => array(
        'title'   => __( 'Payment process', 'woocommerce-nestpay' ),
        'label'   => __( 'Enable automatic redirect to NestPay payment form', 'woocommerce-nestpay' ),
        'type'    => 'checkbox',
        'default' => 'yes',
    ),
    // API CREDENTIALS.
    'api'               => array(
        'title'       => __( 'API Settings', 'woocommerce-nestpay' ),
        'type'        => 'title',
        'description' => __( 'Enter your NestPay API credentials in order to process payments', 'woocommerce-nestpay' ),
    ),
    // LIVE CREDENTIALS.
    'merchant_id'       => array(
        'title'       => __( 'Merchant ID', 'woocommerce-nestpay' ),
        'type'        => 'text',
        'description' => __( 'Please enter your Merchant ID. This is needed in order to process payments', 'woocommerce-nestpay' ),
        // 'desc_tip'      => true
    ),
    'username'          => array(
        'title'       => __( 'Username', 'woocommerce-nestpay' ),
        'type'        => 'text',
        'description' => __( 'Please enter your Username. This is needed in order to process payments', 'woocommerce-nestpay' ),
        // 'desc_tip'      => true
    ),
    'password'          => array(
        'title'       => __( 'Password', 'woocommerce-nestpay' ),
        'type'        => 'password',
        'description' => __( 'Please enter your Password. This is needed in order to process payments', 'woocommerce-nestpay' ),
        // 'desc_tip'      => true
    ),
    'payment_url'       => array(
        'title'       => __( 'Payment URL', 'woocommerce-nestpay' ),
        'type'        => 'text',
        'description' => __( 'Please enter your Username. This is needed in order to process payments', 'woocommerce-nestpay' ),
        'desc_tip'    => true,
    ),
    'api_url'           => array(
        'title'       => __( 'API URL', 'woocommerce-nestpay' ),
        'type'        => 'text',
        'description' => __( 'Please enter your Username. This is needed in order to take payments', 'woocommerce-nestpay' ),
        'desc_tip'    => true,
    ),
    'store_key'         => array(
        'title'       => __( 'Store key', 'woocommerce-nestpay' ),
        'type'        => 'password',
        'description' => __( 'Please enter your Password. This is needed in order to take payments', 'woocommerce-nestpay' ),
        // 'desc_tip'      => true
    ),
    // TEST Credentials.
    'test_merchant_id'  => array(
        'title'       => sprintf(
            '%s %s',
            __( 'Sandbox', 'woocommerce-nestpay' ),
            __( 'Merchant ID', 'woocommerce-nestpay' )
        ),
        'type'        => 'text',
        'description' => __( 'Please enter your Merchant ID. This is needed in order to take payments', 'woocommerce-nestpay' ),
        // 'desc_tip'      => true
    ),
    'test_username'     => array(
        'title'       => sprintf(
            '%s %s',
            __( 'Sandbox', 'woocommerce-nestpay' ),
            __( 'Username', 'woocommerce-nestpay' )
        ),
        'type'        => 'text',
        'description' => __( 'Please enter your Username. This is needed in order to take payments', 'woocommerce-nestpay' ),
        // 'desc_tip'      => true
    ),
    'test_password'     => array(
        'title'       => sprintf(
            '%s %s',
            __( 'Sandbox', 'woocommerce-nestpay' ),
            __( 'Password', 'woocommerce-nestpay' )
        ),
        'type'        => 'password',
        'description' => __( 'Please enter your Password. This is needed in order to take payments', 'woocommerce-nestpay' ),
        // 'desc_tip'      => true
    ),
    'test_payment_url'  => array(
        'title'       => sprintf(
            '%s %s',
            __( 'Sandbox', 'woocommerce-nestpay' ),
            __( 'Payment URL', 'woocommerce-nestpay' )
        ),
        'type'        => 'text',
        'description' => __( 'Please enter your Username. This is needed in order to take payments', 'woocommerce-nestpay' ),
        'desc_tip'    => true,
    ),
    'test_api_url'      => array(
        'title'       => sprintf(
            '%s %s',
            __( 'Sandbox', 'woocommerce-nestpay' ),
            __( 'API URL', 'woocommerce-nestpay' )
        ),
        'type'        => 'text',
        'description' => __( 'Please enter your Username. This is needed in order to take payments', 'woocommerce-nestpay' ),
        'desc_tip'    => true,
    ),
    'test_store_key'    => array(
        'title'       => __( 'Sandbox Store key', 'woocommerce-nestpay' ),
        'type'        => 'password',
        'description' => __( 'Please enter your Password. This is needed in order to take payments', 'woocommerce-nestpay' ),
        // 'desc_tip'      => true
    ),
    // STORE SETTINGS.
    'store'             => array(
        'title'       => __( 'Store Settings', 'woocommerce-nestpay' ),
        'type'        => 'title',
        'description' => __( 'Store settings define how payments are handled', 'woocommerce-nestpay' ),
    ),
    'store_currency'    => array(
        'title'       => __( 'Store Currency', 'woocommerce-nestpay' ),
        'type'        => 'select',
        'options'     => wcnpg_get_currencies(),
        'default'     => 0,
        'description' => __( 'Select a currency to use - defaults to WooCommerce currency', 'woocommerce-nestpay' ),
        'class'       => 'select2',
    ),
    'store_type'        => array(
        'title'       => __( 'Store type', 'woocommerce-nestpay' ),
        'type'        => 'select',
        'options'     => array(
            '3d_pay_hosting' => __( 'Hosted page', 'woocommerce-nestpay' ),
            // '3d'             => __('Inline form', 'woocommerce-nestpay')
        ),
        'description' => __(
            'Store page defines how the gateway works.
            Hosted page will redirect to a bank portal, inline form will display the CC form on your website',
            'woocommerce-nestpay'
        ),
    ),
    'store_transaction' => array(
        'title'       => __( 'Transaction type', 'woocommerce-nestpay' ),
        'type'        => 'select',
        'options'     => array(
            'Automatic' => __( 'Automatic', 'woocommerce-nestpay' ),
            'PreAuth'   => __( 'Reserve funds (authorize)', 'woocommerce-nestpay' ),
            'Auth'      => __( 'Debit funds (Capture)', 'woocommerce-nestpay' ),
        ),
        'default'     => 'Automatic',
        'description' => sprintf(
            '%s<br>%s',
            esc_html__( 'PreAuth reserves the funds on the cardholder\'s account, Auth debits the funds from the cardholder\'s account.', 'woocommerce-nestpay' ),
            esc_html__( 'Setting this to automatic will use PreAuth for physical goods and Auth for digital goods', 'woocommerce-nestpay' ),
        ),
    ),
    // SECURITY SETTINGS.
    'security'          => array(
        'title'       => __( 'hCaptcha', 'woocommerce-nestpay' ),
        'type'        => 'title',
        'description' => __( 'hCaptcha settings are used to prevent automated payment form submissions', 'woocommerce-nestpay' ),
    ),
    'hcaptcha_key'      => array(
        'title'   => __( 'hCaptcha site key', 'woocommerce-nestpay' ),
        'type'    => 'text',
        'default' => '',
        // 'description'   => __('Order status after successful Pre-Authorization', 'woocommerce-nestpay'),
    ),
    'hcaptcha_secret'   => array(
        'title'   => __( 'hCaptcha secret', 'woocommerce-nestpay' ),
        'type'    => 'text',
        'default' => '',
        // 'description'   => __('Order status after successful Pre-Authorization', 'woocommerce-nestpay'),
    ),

);
