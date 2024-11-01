<?php

namespace WKO\Services\Koin\Requests\Orders;

use WKO\Services\Koin\Authentication;
use WKO\Services\Koin\Requests\InterfaceRequest;
use WKO\Services\Koin\Requests\Request;

/**
 * Name: Get
 * Get koin orders
 * @package Koin\Requests\Orders
 * @param int $order_id
 * @since 1.0.0
 */
class Get extends Request implements InterfaceRequest
{
    public function __construct( $order_id = false )
    {
        $this->endpoint = $this->get_endpoint( '/payment/v1/orders', [ $order_id ] );
        $this->header   = [];
        
        $this->method  = 'GET';
    }

    public function handle_request()
    {
        return $this->send();
    }
}
