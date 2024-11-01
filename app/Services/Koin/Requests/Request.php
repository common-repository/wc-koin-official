<?php

namespace WKO\Services\Koin\Requests;

use WKO\Controllers\Logs;
use WKO\Helpers\Config as HelpersConfig;
use WKO\Services\Koin\Config;

/**
 * Name: Request
 * Abstract class for requests
 * @package Koin\Requests
 * @since 1.0.0
 */
abstract class Request
{
    protected $body;
    protected $header;
    protected $method;
    protected $endpoint;

    /**
     * Send requests
     * @param array $header
     * @param array $body
     * @param string $url
     */
    protected function send()
    {
        $_header = [
            'Accept'           => 'application/json',
            'Content-Type'     => 'application/json',
            'W-Module-Version' => HelpersConfig::__version(),
            'Authorization'    => $this->get_auth()
        ];

        $header = array_merge( $_header, $this->header );

        if ( ! $this->body ) {
            $this->body = [];
        }

        $args = [
            'headers' => $header,
            'timeout' => 10000,
            'body'    => json_encode($this->body),
            'method'  => $this->method
        ];

        if (get_option('wc_koin_settings_environment') === 'sandbox') {
            $args['user-agent'] = 'koin-oficial';
        }

        $log = new Logs;
        $response = wp_remote_request( $this->endpoint, $args );

        if (!str_contains($this->endpoint, 'tokenize')) {
            $log->set_request_logs( "KOIN REQUEST SENT", print_r( [ $this->endpoint, $args ], true ) );
        }

        $log->set_request_logs( "KOIN RESPONSE RECEIVED", print_r( $response, true ) );

        return $response;
    }

    protected function get_auth()
    {
        $enviroment = get_option('wc_koin_settings_environment');

        if ($enviroment === 'sandbox') {
            $token = get_option('wc_koin_settings_secret_key_test');
        } else {
            $token = get_option('wc_koin_settings_secret_key');
        }

        return "Bearer $token";
    }

    /**
     * Get the full request url
     * @param string $base
     * @param array $parameters
     * @return string
     */
    protected function get_endpoint( $base, $parameters = [] )
    {
        $url = Config::request_domain() . $base;
        foreach( $parameters as $param ) {
            if ( $param ) {
                $url .= "/{$param}";
            }
        }

        return $url;
    }
}
