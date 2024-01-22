<?php
/**
 * Plugin assets
 *
 * @package WooCommerce Sync Service
 */

defined( 'ABSPATH' ) || exit;

return array(
    'version'   => WCNPG_VERSION,
    'priority'  => 50,
    'dist_path' => WCNPG_PLUGIN_PATH . 'dist',
    'dist_uri'  => plugins_url( 'dist', WCNPG_PLUGIN_BASENAME ),
    'assets'    => array(
        'admin' => array(
            'styles'  => array( 'styles/admin.css' ),
            'scripts' => array( 'scripts/admin.js' ),
        ),
        'front' => array(
            'styles'  => array(),
            'scripts' => array( 'scripts/main.js' ),
        ),
    ),
);
