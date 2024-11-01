<?php

namespace WKO\Services\Koin\Requests\Card;

use WKO\Services\Koin\Config;
use WKO\Services\Koin\Requests\InterfaceRequest;
use WKO\Services\Koin\Requests\Request;

/**
 * Name: Tokenize
 * Tokenize credit card
 * @package Koin\Requests\Card
 * @param int $order_id
 * @since 1.0.0
 */

class Tokenize extends Request implements InterfaceRequest
{
    public function __construct($card_fields = [])
    {
        $this->endpoint = $this->get_tokenize_endpoint( '/v1/payment/tokenize', [] );
        $this->header   = [];
        $this->body = [
            'transaction' => [
                'reference_id' => 'WC-' . time()
            ],
            'card' => $card_fields
        ];

        $this->method  = 'POST';
    }

    protected function get_tokenize_endpoint( $base, $parameters = [] )
    {
        $url = Config::is_test_mode() ? Config::request_domain() : 'https://api-secure.koin.com.br';
        $url .= $base;

        foreach ( $parameters as $param ) {
            if ( $param ) {
                $url .= "/{$param}";
            }
        }

        return $url;
    }

    public function handle_request()
    {
        return $this->send();
    }
}
