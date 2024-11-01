<?php

namespace WKO\Controllers\Orders;

use WKO\Controllers\Render\EditOrders as RenderEditOrders;
use WKO\Model\Options;

/**
 * Name: Edit Orders
 * Order controller
 * @package Controllers\Orders
 * @since 1.0.0
 */
class EditOrders
{
    private $opt;

    public function __construct()
    {
        $this->opt = new Options;
        add_action( 'woocommerce_admin_order_data_after_billing_address', [ $this, 'koin_order_label' ], 10, 1 );
    }

    /**
     * Call render to order edit page
     * @since 1.0.0
     * @param object
     * @return void
     */
    public function koin_order_label( $order )
    {
        if ( get_option('wc_koin_settings_sync') === 'yes' ) {

            $data = $order->get_data();
            
            if ( isset( $data['payment_method'] ) ) {
                $method = $data['payment_method'];
                
                if ( array_intersect( [ $method ], [ "wc-koin-billet", "wc-koin-credit", "wc-koin-pix" ] ) ) {
                    new RenderEditOrders( $data );
                }
            } 
            
        }
    }
}