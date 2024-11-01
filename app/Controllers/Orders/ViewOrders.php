<?php

namespace WKO\Controllers\Orders;

use WKO\Controllers\Render\ViewOrders as RenderViewOrders;

/**
 * Name: View Orders
 * Order Controller
 * @package Controllers\Orders
 * @since 1.0.0
 */
class ViewOrders
{
    public function __construct()
    {
        add_action( 'woocommerce_order_details_after_order_table', [ $this, 'add_koin_order_details' ], 10, 1 );
    }

    /**
     * Add Koin order details
     * @since 1.0.0
     * @param mixed $order
     * @return RenderViewOrders
     */
    public function add_koin_order_details( $order )
    {
        if ( array_intersect( [ 'wc-koin-billet' ], [ $order->get_payment_method() ] ) ) {
            return new RenderViewOrders( $order );
        }
    }
}