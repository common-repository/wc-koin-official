<?php

namespace WKO\Controllers\Render;

use WKO\Controllers\Render;

/**
 * Name: Koin Orders
 * Render the edit order fields
 * @package Controller\Render
 * @since 1.0.0
 */
class KoinOrders extends Render
{
    private $orders;
    
    public function __construct( $current_page )
    {
        $this->get_koin_orders( $current_page );
        $this->render( '/templates/myaccount/koin-orders.php', $this->orders );
    }

    /**
     * Load Koin orders
     * @since 1.0.0
     * @return void
     */
    private function get_koin_orders( $current_page )
    {
        $current_page    = empty( $current_page ) ? 1 : absint( $current_page );
		$customer_orders = wc_get_orders(
			apply_filters(
				'woocommerce_my_account_my_orders_query', [
					'customer'    => get_current_user_id(),
					'page'        => $current_page,
					'paginate'    => true,
                    'payment_method' => [ 
                        "wc-koin-billet",
                        "wc-koin-credit",
                        "wc-koin-pix"
                    ],
                    'status'  => [ 
                        'on-hold', 'processing', 'cancelled', 'completed', 'wc-awaiting-analysis', 'wc-awaiting-payment'
                    ]
                ]
            ),
		);

        $this->orders = [
            'current_page'    => absint( $current_page ),
            'has_orders'      => 0 < $customer_orders->total,
            'customer_orders' => $customer_orders
        ];
    }

}