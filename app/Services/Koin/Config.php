<?php

namespace WKO\Services\Koin;

use WKO\Model\Options;

/**
 * Name: Config
 * Handles general service information
 * @package Services\Koin
 * @since 1.0.0
 */
class Config
{
    /**
     * Sandbox URL
     * @since 1.0.0
     */
    const WKO_SANDBOX_DOMAIN = 'https://api-sandbox.koin.com.br';

    /**
     * Production URL
     * @since 1.0.0
     */
    const WKO_PRODUCTION_DOMAIN = 'https://api-payments.koin.com.br';

    /**
     * Gets the selected mode
     * @since 1.0.0
     * @return boolean
     */
    public static function is_test_mode()
    {
        $woo_opt = get_option( 'wc_koin_settings_environment' );
        if ( $woo_opt === 'sandbox' ) {
            return true;
        }

        return false;
    }

    /**
     * Get url for the selected mode
     * @since 1.0.0
     * @return string
     */
    public static function request_domain()
    {
        $sandbox = self::is_test_mode();

        if ( $sandbox ) {
            return self::WKO_SANDBOX_DOMAIN;
        }

        return self::WKO_PRODUCTION_DOMAIN;
    }

    /**
     * Get secret key
     * @since 1.0.0
     * @return string
     */
    public static function secret_key()
    {
        $mode = self::selected_mode() ? 'test_secret_key' : 'secret_key';

        $options = new Options;

        $woo_opt = $options->get_gateways_option( $mode  );
        if ( $woo_opt ) {
            return $woo_opt;
        }

        return false;
    }

    /**
     * Get secret key
     * @since 1.0.0
     * @return string
     */
    public static function consumer_key()
    {
        $mode = self::selected_mode() ? 'test_consumer_key' : 'consumer_key';

        $options = new Options;

        $woo_opt = $options->get_gateways_option( $mode  );
        if ( $woo_opt ) {
            return $woo_opt;
        }

        return false;
    }

    public static function store_iss()
    {
        $mode = self::selected_mode() ? 'test_store_iss' : 'store_iss';

        $options = new Options;

        $woo_opt = $options->get_gateways_option( $mode );
        if ( $woo_opt ) {
            return $woo_opt;
        }

        return false;
    }
}