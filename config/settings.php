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
        'title'   => __( 'Enable/Disable', 'wc-serbian-nestpay' ),
        'label'   => __( 'Enable NestPay', 'wc-serbian-nestpay' ),
        'type'    => 'checkbox',
        'default' => 'no',
    ),
    'title'             => array(
        'title'       => __( 'Title', 'wc-serbian-nestpay' ),
        'type'        => 'text',
        'description' => __( 'This controls the title which the user sees during checkout.', 'wc-serbian-nestpay' ),
        'default'     => 'NestPay',
        'desc_tip'    => true,
    ),
    'description'       => array(
        'title'       => __( 'Description', 'wc-serbian-nestpay' ),
        'type'        => 'text',
        'description' => __( 'This controls the description which the user sees during checkout.', 'wc-serbian-nestpay' ),
        'default'     => __( 'Pay with your credit-card using NestPay', 'wc-serbian-nestpay' ),
        'desc_tip'    => true,
    ),
    'banking'           => array(
        'title' => __( 'Bank settings', 'wc-serbian-nestpay' ),
        'type'  => 'title',
    ),
    'bank'              => array(
        'title'       => __( 'Bank', 'wc-serbian-nestpay' ),
        'type'        => 'image_select',
        'description' => __( 'Select the bank you want to use', 'wc-serbian-nestpay' ),
        'options'     => array(
            'intesa-rs'     => array(
                'image' => 'images/intesa-rs.png',
                'title' => __( 'Banca Intesa', 'wc-serbian-nestpay' ),
            ),
            'commercial-rs' => array(
                'image'    => 'images/komercijalna-rs.png',
                'title'    => __( 'Commercial Bank', 'wc-serbian-nestpay' ),
                'disabled' => true,
            ),
		),
        'default'     => 'intesa-rs',
	),
    'testmode'          => array(
        'title'       => __( 'NestPay Sandbox', 'wc-serbian-nestpay' ),
        'label'       => __( 'Enable NestPay Sandbox', 'wc-serbian-nestpay' ),
        'type'        => 'checkbox',
        'description' => __( 'NestPay sandbox can be used to test payments', 'wc-serbian-nestpay' ),
        'default'     => 'no',
    ),
    // API CREDENTIALS.
    'api'               => array(
        'title'       => __( 'API Settings', 'wc-serbian-nestpay' ),
        'type'        => 'title',
        'description' => __( 'Enter your NestPay API credentials in order to process payments', 'wc-serbian-nestpay' ),
    ),
    // LIVE CREDENTIALS.
    'merchant_id'       => array(
        'title'       => __( 'Merchant ID', 'wc-serbian-nestpay' ),
        'type'        => 'text',
        'description' => __( 'Please enter your Merchant ID. This is needed in order to process payments', 'wc-serbian-nestpay' ),
    ),
    'username'          => array(
        'title'       => __( 'Username', 'wc-serbian-nestpay' ),
        'type'        => 'text',
        'description' => __( 'Please enter your Username. This is needed in order to process payments', 'wc-serbian-nestpay' ),
    ),
    'password'          => array(
        'title'       => __( 'Password', 'wc-serbian-nestpay' ),
        'type'        => 'password',
        'description' => __( 'Please enter your Password. This is needed in order to process payments', 'wc-serbian-nestpay' ),
    ),
    'store_key'         => array(
        'title'       => __( 'Store key', 'wc-serbian-nestpay' ),
        'type'        => 'password',
        'description' => __( 'Please enter your Password. This is needed in order to take payments', 'wc-serbian-nestpay' ),
    ),
    // TEST Credentials.
    'test_merchant_id'  => array(
        'title'       => sprintf(
            '%s %s',
            __( 'Sandbox', 'wc-serbian-nestpay' ),
            __( 'Merchant ID', 'wc-serbian-nestpay' )
        ),
        'type'        => 'text',
        'description' => __( 'Please enter your Merchant ID. This is needed in order to take payments', 'wc-serbian-nestpay' ),
    ),
    'test_username'     => array(
        'title'       => sprintf(
            '%s %s',
            __( 'Sandbox', 'wc-serbian-nestpay' ),
            __( 'Username', 'wc-serbian-nestpay' )
        ),
        'type'        => 'text',
        'description' => __( 'Please enter your Username. This is needed in order to take payments', 'wc-serbian-nestpay' ),
    ),
    'test_password'     => array(
        'title'       => sprintf(
            '%s %s',
            __( 'Sandbox', 'wc-serbian-nestpay' ),
            __( 'Password', 'wc-serbian-nestpay' )
        ),
        'type'        => 'password',
        'description' => __( 'Please enter your Password. This is needed in order to take payments', 'wc-serbian-nestpay' ),
    ),
    'test_store_key'    => array(
        'title'       => __( 'Sandbox Store key', 'wc-serbian-nestpay' ),
        'type'        => 'password',
        'description' => __( 'Please enter your Password. This is needed in order to take payments', 'wc-serbian-nestpay' ),
    ),
    // STORE SETTINGS.
    'store'             => array(
        'title'       => __( 'Store Settings', 'wc-serbian-nestpay' ),
        'type'        => 'title',
        'description' => __( 'Store settings define how payments are handled', 'wc-serbian-nestpay' ),
    ),
    'auto_redirect'     => array(
        'title'   => __( 'Payment process', 'wc-serbian-nestpay' ),
        'label'   => __( 'Enable automatic redirect to NestPay payment form', 'wc-serbian-nestpay' ),
        'type'    => 'checkbox',
        'default' => 'yes',
    ),
    'store_currency'    => array(
        'title'       => __( 'Store Currency', 'wc-serbian-nestpay' ),
        'type'        => 'select',
        'options'     => get_nestpay_currency_options(),
        'default'     => 0,
        'description' => __( 'Select a currency to use - defaults to WooCommerce currency', 'wc-serbian-nestpay' ),
        'class'       => 'select2',
    ),
    'store_transaction' => array(
        'title'       => __( 'Transaction type', 'wc-serbian-nestpay' ),
        'type'        => 'select',
        'options'     => array(
            'Automatic' => __( 'Automatic', 'wc-serbian-nestpay' ),
            'PreAuth'   => __( 'Reserve funds (authorize)', 'wc-serbian-nestpay' ),
            'Auth'      => __( 'Debit funds (Capture)', 'wc-serbian-nestpay' ),
        ),
        'default'     => 'Automatic',
        'description' => sprintf(
            '%s<br>%s',
            esc_html__( 'PreAuth reserves the funds on the cardholder\'s account, Auth debits the funds from the cardholder\'s account.', 'wc-serbian-nestpay' ),
            esc_html__( 'Setting this to automatic will use PreAuth for physical goods and Auth for digital goods', 'wc-serbian-nestpay' ),
        ),
    ),
    // ADVANCED SETTINGS.
    'advanced'          => array(
        'title'       => __( 'Advanced Settings', 'wc-serbian-nestpay' ),
        'type'        => 'title',
        'description' => '',
    ),
    'debug'             => array(
        'title'       => __( 'Debug log', 'wc-serbian-nestpay' ),
        'type'        => 'checkbox',
        'label'       => __( 'Enable logging', 'wc-serbian-nestpay' ),
        'default'     => 'no',
        'description' => sprintf(
            // translators: %s log file path.
            __(
                'Log NestPay events, inside %s Note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.',
                'wc-serbian-nestpay'
            ),
            '<code>' . WC_Log_Handler_File::get_log_file_path( 'nestpay' ) . '</code><br>'
        ),
    ),
);
