<?php

namespace WKO\Helpers;

use WKO\API\Routes;
use WKO\Controllers\Notifications;
use WKO\Controllers\Woocommerce;

/**
 * Name: Functions
 * Handle hooks/filters functions
 * @package Helpers
 * @since 1.0.0
 */
class Functions
{
    /**
     * Load admin scripts and styles
     * @since 1.0.0
     * @return void
     */
    public static function enqueue_admin_scripts()
    {
        wp_enqueue_script( 'admin', Config::__dist( 'admin/index.js' ) );
        wp_enqueue_style( 'admin', Config::__dist( 'admin/index.css' ) );
    }

    /**
     * Load theme scripts and styles
     * @since 1.0.0
     * @return void
     */
    public static function enqueue_theme_scripts()
    {
        if (function_exists('is_checkout') && is_checkout()) {
            wp_enqueue_script( 'theme', Config::__dist( 'theme/index.js' ), ['jquery', 'wp-hooks', 'wc-blocks-checkout'] );
            wp_enqueue_style( 'theme', Config::__dist( 'theme/index.css' ) );
            wp_enqueue_script('koin-finger-print', 'https://securegtm.despegar.com/risk/fingerprint/statics/track-min.js');
        }
    }

    /**
     * Add HTML attributes to script tag
     * @since 1.2.4
     * @param string $script
     * @return string
     */
    public static function add_script_attributes($script)
    {

        $enviroment = get_option('wc_koin_settings_environment');
        $org_id = $enviroment === 'production' ? get_option('wc_koin_settings_org_id') : get_option('wc_koin_settings_org_id_test');

        if (str_contains($script, 'koin-finger-print-js') && $org_id) {
            if ($org_id) {
                $script = str_replace('id="koin-finger-print-js"', 'id="deviceId_fp" org_id="'. $org_id .'"', $script);
            } else {
                return '';
            }
        }

        return $script;
    }

    /**
     * Load plugin text domain
     * @since 1.0.0
     * @return void
     */
    public static function initialize()
    {
        $locale = apply_filters( 'plugin_locale', get_locale(), WKO_PLUGIN_SLUG );

		load_textdomain( WKO_PLUGIN_SLUG, Config::__dir() . "/languages/" . WKO_PLUGIN_SLUG . "-$locale.mo" );
		load_plugin_textdomain( WKO_PLUGIN_SLUG, false, Config::__dir() . '/languages/' );
    }

    /**
     * Init Woocommerce classes
     * @since 1.0.0
     * @return Woocommerce
     */
    public static function woo_init()
    {
        if (!class_exists('WooCommerce')) {
            new Notifications(
                WKO_PLUGIN_NAME,
                sprintf( ': %s',
                    __( 'The plugin depends on WooCommerce to work!', 'wc-koin-official')
                ),
                'error'
            );

            return;
        }

        $woocommerce = new Woocommerce;
        $woocommerce->initialize();
    }

    /**
     * Create extra link on plugins page
     * @since 1.0.0
     * @param array $arr
     * @param string $name
     * @return array
     */
    public static function settings_link( $arr, $name ){

        if( $name === Config::__base() && class_exists( 'WooCommerce' ) ) {

            $label = sprintf( '<a href="admin.php?page=wc-settings&tab=koin-settings" id="deactivate-koin-official-payments" aria-label="%s">%s</a>',
                __( 'Settings for Koin Official Payments', 'wc-koin-official' ),
                __( 'Koin Settings', 'wc-koin-official' )
            );

            $arr['settings'] = $label;
        }

        return $arr;
    }

    /**
     * handle plugins actions
     * @since 1.0.0
     * @return void
     */
    public static function handle_actions()
    {
        $action_name = WKO_PLUGIN_PREFIX . '_action';
        $vars = [];

        if ( is_array( $_REQUEST ) && isset( $_REQUEST[ $action_name ] ) ) {
            $vars = array_map('sanitize_text_field', $_REQUEST);
        }

        if ( is_array( $vars ) && isset( $vars[ $action_name ] ) ) {
            $controller = Utils::parse_controller( $vars[ $action_name ] );

            new $controller( $vars );
        }
    }

    /**
     * Shows compatibility version warning
     * @since 1.2.0
     * @return void
     */
    public static function show_crossversion_messa()
    {
        $message = sprintf( '%s <a href="#">%s</a>.',
            __( 'Essa versão utiliza um novo mecanismo de autenticação! Caso não consiga se autenticar entre em contato conosco', 'wc-koin-official' ),
            __( 'neste link', 'wc-koin-official' )
        );

        new Notifications( 'Koin Official Payments:', $message, 'warnning' );
    }

    public static function registerRestAPI(): void
    {
        $routes = new Routes();
        $routes->register();
    }
}
