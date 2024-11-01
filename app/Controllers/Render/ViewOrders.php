<?php

namespace WKO\Controllers\Render;

use WKO\Controllers\Render;
use WKO\Model\PostMeta;

/**
 * Name: Koin Orders
 * Render the edit order fields
 * @package Controller\Render
 * @since 1.0.0
 */
class ViewOrders extends Render
{
    private $order;
    
    public function __construct( $order )
    {
        $this->order = $order;
        $this->render( '/templates/myaccount/order-details.php', $this->get_order_details() );
    }

    /**
     * Get order details
     * @since 1.0.0
     * @return array
     */
    private function get_order_details()
    {
        $metas = $this->get_meta();
        return [
            'koin_order_id'     => $metas['_order_id'] ? $metas['_order_id'] : __( "Waiting for Koin's reply", 'wc-koin-official' ),
            'koin_order_link'   => $metas['_order_page'],
            'koin_payment_link' => $metas['_payment_link'],
            'method'            => $this->order->get_payment_method_title(),
            'installments'      => $metas['_installments'] ? $metas['_installments'] : __( "Undefined", 'wc-koin-official' ),
        ];
    }

    /**
     * Get order metas
     * @since 1.0.0
     * @return array
     */
    private function get_meta()
    {
        $metas = [
            '_payment_link',
            '_order_page',
            '_order_id',
            '_installments'
        ];

        $result = [];

        $postmeta = new PostMeta;

        foreach( $metas as $meta ) {
            $value = $postmeta->get( $this->order->get_id(), $meta );

            if ( ! $value ) {
                $value = "";
            } 

            $result[$meta] =  $value;
        }

        return $result;
    }

}