<?php

namespace WKO\Helpers;

use WKO\Controllers\Woocommerce;

/**
 * Name: Hooks
 * Call the actions and filter
 * @package Helpers
 * @since 1.0.0
 */

add_action( 'init', [
    'WKO\Helpers\Functions',
    'initialize'
] );

add_action( 'wp_enqueue_scripts', [
    'WKO\Helpers\Functions',
    'enqueue_theme_scripts'
] );

add_action( 'admin_enqueue_scripts', [
    'WKO\Helpers\Functions',
    'enqueue_admin_scripts'
] );

add_action( 'init', [
    'WKO\Helpers\Functions',
    'woo_init'
] );

add_action( 'init', [
    'WKO\Helpers\Functions',
    'handle_actions'
] );

add_filter( 'plugin_action_links', [
    'WKO\Helpers\Functions',
    'settings_link'
], 10, 2 );


add_action('rest_api_init', [
    new Functions,
    'registerRestAPI'
]);

add_filter('script_loader_tag', [
    new Functions,
    'add_script_attributes'
], 10, 1);


add_filter('woocommerce_payment_gateways', [
    new Woocommerce,
    'registerPaymentGateways'
], 10, 1);

add_action('woocommerce_blocks_loaded', [
    new WooCommerce,
    'loadWooCommerceBlocks'
], 10);


add_action( 'woocommerce_blocks_loaded', [new WooCommerce, 'registerGatewayUpdateTotal'] );
