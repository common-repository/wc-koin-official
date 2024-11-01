<?php

namespace WKO\Services\Koin\Requests\Orders;

use WKO\Services\Koin\Authentication;
use WKO\Services\Koin\Requests\InterfaceRequest;
use WKO\Services\Koin\Requests\Request;

/**
 * Name: Cancel
 * Cancel koin orders
 * @package Koin\Requests\Orders
 * @param int $order_id
 * @since 1.0.0
 */

class Cancel extends Request implements InterfaceRequest
{
    public function __construct( $order_id )
    {
        $this->endpoint = $this->get_endpoint( '/payment/v1/orders', [ $order_id, 'cancel' ] );
        $this->header   = [];
        
        $this->method  = 'PUT';
    }

    public function handle_request()
    {
        return $this->send();
    }
}
