<?php

namespace WKO\Services\Koin\Requests\Orders;

use WKO\Services\Koin\Requests\InterfaceRequest;
use WKO\Services\Koin\Requests\Request;

/**
 * Name: Create
 * Create a new koin payment
 * @package Koin\Requests\Orders
 * @param array $body
 * @since 1.0.0
 */
class Create extends Request implements InterfaceRequest
{
    public function __construct( $body )
    {
        $this->endpoint = $this->get_endpoint( '/payment/v1/orders' );
        $this->body     = $body;
        $this->header   = [];
        
        $this->method  = 'POST';
    }

    public function handle_request()
    {
        return $this->send();
    }
}
