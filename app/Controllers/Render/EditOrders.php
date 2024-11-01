<?php

namespace WKO\Controllers\Render;

use WKO\Controllers\Render;
use WKO\Model\PostMeta;

/**
 * Name: Order Fields
 * Render the edit order fields
 * @package Controller\Render
 * @param array $order
 * @since 1.0.0
 */
class EditOrders extends Render
{
    private $order;
    
    public function __construct( $order )
    {
        $this->order = $order;
        $this->render( '/orders.php', $this->get_order_data() );
    }

    /**
     * Get order data
     * @since 1.0.0
     * @return array
     */
    private function get_order_data()
    {
        $meta = new PostMeta;
        
        $koin_order_id     = $meta->get( $this->order['id'], "_order_id" );
        $koin_insttalments = $meta->get( $this->order['id'], "_installments" );
        $koin_payment      = $meta->get( $this->order['id'], "_payment_link" );

        $_koin_order_id    = $koin_order_id ? $koin_order_id : __( "Waiting for Koin's reply", 'wc-koin-official' );

        return [
            'order' => [ 
                'koin_order_id' => $_koin_order_id,
                'order_id'      => $this->order['id']
            ],
            'method'        => $this->order['payment_method_title'],
            'koin_order_id' => $_koin_order_id,
            'payment_link'  => $koin_payment ? $koin_payment : false,
            'installments'  => $koin_insttalments ? $koin_insttalments : __( "Undefined", 'wc-koin-official' ),
        ];
    }
}