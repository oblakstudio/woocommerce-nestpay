<?php

use function Oblak\NPG\getGatewayCurrencies;

$statuses = wc_get_order_statuses();

return [
    'enabled' => [
        'title'         => __('Enable/Disable', 'woocommerce-nestpay'),
        'label'         => __('Enable NestPay', 'woocommerce-nestpay'),
        'type'          => 'checkbox',
        'default'       => 'no',
    ],
    'title' => [
        'title'         => __('Title', 'woocommerce-nestpay'),
        'type'          => 'text',
        'description'   => __('This controls the title which the user sees during checkout.', 'woocommerce-nestpay'),
        'default'       => 'NestPay',
        'desc_tip'      => true
    ],
    'description' => [
        'title'         => __('Description', 'woocommerce-nestpay'),
        'type'          => 'text',
        'description'   => __('This controls the description which the user sees during checkout.', 'woocommerce-nestpay'),
        'default'       => __('Pay with your credit-card using NestPay', 'woocommerce-nestpay'),
        'desc_tip'      => true
    ],
    'advanced' => [
        'title'         => __('Advanced Settings', 'woocommerce-nestpay'),
        'type'          => 'title',
        'description'   => ''
    ],
    'testmode' => [
        'title'         => __('NestPay Sandbox', 'woocommerce-nestpay'),
        'label'         => __('Enable NestPay Sandbox', 'woocommerce-nestpay'),
        'type'          => 'checkbox',
        'description'   => __('NestPay sandbox can be used to test payments', 'woocommerce-nestpay'),
        'default'       => 'no',
    ],
    'debug' => [
        'title'         => __( 'Debug log', 'woocommerce-nestpay' ),
        'type'          => 'checkbox',
        'label'         => __( 'Enable logging', 'woocommerce-nestpay' ),
        'default'       => 'no',
        'description'   => sprintf(
            __('Log NestPay events, inside %s Note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.', 'woocommerce-nestpay'),
            '<code>' . WC_Log_Handler_File::get_log_file_path( 'nestpay' ) . '</code>'
        ),
    ],
    'auto_redirect' => [
        'title'         => __('Payment process', 'woocommerce-nestpay'),
        'label'         => __('Enable automatic redirect to NestPay payment form', 'woocommerce-nestpay'),
        'type'          => 'checkbox',
        'default'       => 'yes',
    ],
    // API CREDENTIALS
    'api' => [
        'title'         => __('API Settings', 'woocommerce-nestpay'),
        'type'          => 'title',
        'description'   => __('Enter your NestPay API credentials in order to process payments', 'woocommerce-nestpay'),
    ],
    // LIVE CREDENTIALS
    'merchant_id' => [
        'title'         => __('Merchant ID', 'woocommerce-nestpay'),
        'type'          => 'text',
        'description'   => __('Please enter your Merchant ID. This is needed in order to process payments', 'woocommerce-nestpay'),
        // 'desc_tip'      => true
    ],
    'username' => [
        'title'         => __('Username', 'woocommerce-nestpay'),
        'type'          => 'text',
        'description'   => __('Please enter your Username. This is needed in order to process payments', 'woocommerce-nestpay'),
        // 'desc_tip'      => true
    ],
    'password' => [
        'title'         => __('Password', 'woocommerce-nestpay'),
        'type'          => 'password',
        'description'   => __('Please enter your Password. This is needed in order to process payments', 'woocommerce-nestpay'),
        // 'desc_tip'      => true
    ],
    'payment_url' => [
        'title'         => __('Payment URL', 'woocommerce-nestpay'),
        'type'          => 'text',
        'description'   => __('Please enter your Username. This is needed in order to process payments', 'woocommerce-nestpay'),
        'desc_tip'      => true
    ],
    'api_url' => [
        'title'         => __('API URL', 'woocommerce-nestpay'),
        'type'          => 'text',
        'description'   => __('Please enter your Username. This is needed in order to take payments', 'woocommerce-nestpay'),
        'desc_tip'      => true
    ],
    'store_key' => [
        'title'         => __('Store key', 'woocommerce-nestpay'),
        'type'          => 'password',
        'description'   => __('Please enter your Password. This is needed in order to take payments', 'woocommerce-nestpay'),
        // 'desc_tip'      => true
    ],
    // TEST Credentials
    'test_merchant_id' => [
        'title'         => sprintf(
            '%s %s',
            __('Sandbox', 'woocommerce-nestpay'),
            __('Merchant ID', 'woocommerce-nestpay')
        ),
        'type'          => 'text',
        'description'   => __('Please enter your Merchant ID. This is needed in order to take payments', 'woocommerce-nestpay'),
        // 'desc_tip'      => true
    ],
    'test_username' => [
        'title'         => sprintf(
            '%s %s',
            __('Sandbox', 'woocommerce-nestpay'),
            __('Username', 'woocommerce-nestpay')
        ),
        'type'          => 'text',
        'description'   => __('Please enter your Username. This is needed in order to take payments', 'woocommerce-nestpay'),
        // 'desc_tip'      => true
    ],
    'test_password' => [
        'title'         => sprintf(
            '%s %s',
            __('Sandbox', 'woocommerce-nestpay'),
            __('Password', 'woocommerce-nestpay')
        ),
        'type'          => 'password',
        'description'   => __('Please enter your Password. This is needed in order to take payments', 'woocommerce-nestpay'),
        // 'desc_tip'      => true
    ],
    'test_payment_url' => [
        'title'         => sprintf(
            '%s %s',
            __('Sandbox', 'woocommerce-nestpay'),
            __('Payment URL', 'woocommerce-nestpay')
        ),
        'type'          => 'text',
        'description'   => __('Please enter your Username. This is needed in order to take payments', 'woocommerce-nestpay'),
        'desc_tip'      => true
    ],
    'test_api_url' => [
        'title'         => sprintf(
            '%s %s',
            __('Sandbox', 'woocommerce-nestpay'),
            __('API URL', 'woocommerce-nestpay')
        ),
        'type'          => 'text',
        'description'   => __('Please enter your Username. This is needed in order to take payments', 'woocommerce-nestpay'),
        'desc_tip'      => true
    ],
    'test_store_key' => [
        'title'         => __('Sandbox Store key', 'woocommerce-nestpay'),
        'type'          => 'password',
        'description'   => __('Please enter your Password. This is needed in order to take payments', 'woocommerce-nestpay'),
        // 'desc_tip'      => true
    ],
    // STORE SETTINGS
    'store' => [
        'title'         => __('Store Settings', 'woocommerce-nestpay'),
        'type'          => 'title',
        'description'   => __('Store settings define how payments are handled', 'woocommerce-nestpay'),
    ],
    'store_currency' => [
        'title'         => __('Store Currency', 'woocommerce-nestpay'),
        'type'          => 'select',
        'options'       => getGatewayCurrencies(),
        'default'       => 0,
        'description'   => __('Select a currency to use - defaults to WooCommerce currency', 'woocommerce-nestpay'),
        'class'         => 'select2',
    ],
    'store_rsd_fix'  => [
        'title'         => __('Cyrillic RSD fix', 'woocommerce-nestpay'),
        'type'          => 'checkbox',
        'default'       => 'no',
        'description'   => sprintf(
            __('Enable this if RSD is shown in cyrillic (%s)', 'woocommerce-nestpay'),
            "\u{0052}\u{0053}\u{0044}",
        ),
    ],
    'store_type' => [
        'title'         => __('Store type', 'woocommerce-nestpay'),
        'type'          => 'select',
        'options'       => [
            '3d_pay_hosting' => __('Hosted page', 'woocommerce-nestpay'),
            // '3d'             => __('Inline form', 'woocommerce-nestpay')
        ],
        'description'   => __('Store page defines how the gateway works. hosted page will redirect to a bank portal, inline form will display the CC form on your website', 'woocommerce-nestpay'),
    ],
    'store_transaction' => [
        'title'         => __('Transaction type', 'woocommerce-nestpay'),
        'type'          => 'select',
        'options'       => [
            'PreAuth' => __('Reserve funds (authorize)', 'woocommerce-nestpay'),
            'Auth'    => __('Debit funds (Capture)', 'woocommerce-nestpay'),
        ],
        'default'       => 'PreAuth',
        'description'   => __('Store page defines how the gateway works. hosted page will redirect to a bank portal, inline form will display the CC form on your website', 'woocommerce-nestpay'),
    ],
     // SECURITY SETTINGS
    'security' => [
        'title'         => __('hCaptcha', 'woocommerce-nestpay'),
        'type'          => 'title',
        'description'   => __('hCaptcha settings are used to prevent automated payment form submissions', 'woocommerce-nestpay'),
    ],
    'hcaptcha_key' => [
        'title'         => __('hCaptcha site key', 'woocommerce-nestpay'),
        'type'          => 'text',
        'default'       => '',
        // 'description'   => __('Order status after successful Pre-Authorization', 'woocommerce-nestpay'),
    ],
    'hcaptcha_secret' => [
        'title'         => __('hCaptcha secret', 'woocommerce-nestpay'),
        'type'          => 'text',
        'default'       => '',
        // 'description'   => __('Order status after successful Pre-Authorization', 'woocommerce-nestpay'),
    ],
    // ORDER SETTINGS
    // 'order' => [
    //     'title'         => __('Order Settings', 'woocommerce-nestpay'),
    //     'type'          => 'title',
    //     'description'   => __('Order settings define order statuses during various stages of the shopping process', 'woocommerce-nestpay'),
    // ],
    // 'order_id_format'   => [
    //     'title'         => __('Order ID Format', 'woocommerce-nestpay'),
    //     'type'          => 'text',
    //     'default'       => '%oid%',
    //     'description'   => sprintf(
    //         '%s<br>%s<br>
    //         <code>%%oid%%</code> - %s<br>
    //         <code>%%d%%</code> - %s<br>
    //         <code>%%m%%</code> - %s<br>
    //         <code>%%y%%</code> - %s<br>',
    //         __('This controls the format of the order ID generated by the gateway. Change this if you are having issues with previous orders', 'woocommerce-nestpay'),
    //         __('You can use the following replacement variables:', 'woocommerce-nestpay'),
    //         __('Order ID', 'woocommerce'),
    //         __('Day', 'woocommerce'),
    //         __('Month', 'wordpress'),
    //         __('Year', 'woocommerce'),
    //     ),
    // ],
    // 'order_preauth_success' => [
    //     'title'         => __('PreAuth Success', 'woocommerce-nestpay'),
    //     'type'          => 'select',
    //     'options'       => $statuses,
    //     'default'       => 'wc-processing',
    //     'description'   => __('Order status after successful Pre-Authorization', 'woocommerce-nestpay'),
    // ],
    // 'order_preauth_failure' => [
    //     'title'         => __('PreAuth Failure', 'woocommerce-nestpay'),
    //     'type'          => 'select',
    //     'options'       => $statuses,
    //     'default'       => 'wc-failed',
    //     'description'   => __('Order status after failed Pre-Authorization', 'woocommerce-nestpay'),
    // ],
    // 'order_void' => [
    //     'title'         => __('Voided', 'woocommerce-nestpay'),
    //     'type'          => 'select',
    //     'options'       => $statuses,
    //     'default'       => 'wc-cancelled',
    //     'description'   => __('Order status after voiding the transaction', 'woocommerce-nestpay'),
    // ],
    // 'order_postauth_success' => [
    //     'title'         => __('PostAuth Success', 'woocommerce-nestpay'),
    //     'type'          => 'select',
    //     'options'       => $statuses,
    //     'default'       => 'wc-completed',
    //     'description'   => __('Order status after successful Post-Authorization', 'woocommerce-nestpay'),
    // ],
    // 'order_postauth_failure' => [
    //     'title'         => __('PostAuth Failure', 'woocommerce-nestpay'),
    //     'type'          => 'select',
    //     'options'       => $statuses,
    //     'default'       => 'wc-on-hold',
    //     'description'   => __('Order status after failed Post-Authorization', 'woocommerce-nestpay'),
    // ],


];
