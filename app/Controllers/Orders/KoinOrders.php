<?php

namespace WKO\Controllers\Orders;

use WKO\Controllers\Render\KoinOrders as RenderKoinOrders;
use WKO\Model\Options;
use WKO\Model\PostMeta;

/**
 * Name: Koin Orders
 * Order controller
 * @package Controllers\Orders
 * @since 1.0.0
 */
class KoinOrders
{
    public function __construct()
    {
        ## actions
        add_action( 'init', [ $this, 'add_koin_endpoint' ], 999 );
        add_action( 'woocommerce_account_koin_endpoint', [ $this, 'koin_content'], 10, 1 );

        ## filters
        add_filter( 'query_vars', [ $this, 'koin_query_vars' ], 0 );
        add_filter( 'woocommerce_account_menu_items', [ $this, 'add_koin_link_my_account' ] );
        add_filter( 'the_title', [ $this, 'custom_account_endpoint_titles' ], 10, 1 );

        flush_rewrite_rules();
    }

    /**
     * Check if user has Koin order
     * @since 1.0.0
     * @return bool
     */
    private function is_available()
    {
        $my_account = get_option( 'wc_koin_settings_my_account' );

        if ( $my_account == "yes" ) {

            $wc_customer_orders = wc_get_orders( [
                'customer' => get_current_user_id(),
                'payment_method' => [
                    'wc-koin-billet',
                    'wc-koin-credit',
                    'wc-koin-pix'
                ]
            ]);

            if ( is_array( $wc_customer_orders ) && count( $wc_customer_orders ) > 0 ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add koin endpoint
     * @since 1.0.0
     * @return void
     */
    public function add_koin_endpoint()
    {
        add_rewrite_endpoint( 'koin', EP_ROOT | EP_PAGES );
    }

    /**
     * Add koin slug to query vars
     * @since 1.0.0
     * @param array $vars
     * @return array
     */
    public function koin_query_vars( $vars )
    {
        $vars[] = 'koin';
        return $vars;
    }

    /**
     * Add koin menu to myaccount page
     * @since 1.0.0
     * @param array $item
     * @return array
     */
    public function add_koin_link_my_account( $items )
    {
        if ( $this->is_available() ) {
            $items['koin'] = __( 'Koin Payments', 'wc-koin-official' );
        }

        return $items;
    }

    /**
     * Change default page title
     * @since 1.0.0
     * @param string $title
     * @return string
     */
    public function custom_account_endpoint_titles( $title )
    {
        global $wp_query;

        if ( isset( $wp_query->query_vars['koin'] ) && in_the_loop() ) {
            $title = __( 'Koin Payments', 'wc-koin-official' );
        }

        return $title;
    }
    /**
     * Load Koin order page view
     * @since 1.0.0
     * @return void
     */
    public function koin_content( $current_page )
    {
        if ( $this->is_available() ) {

            ## Add new column to order view
            add_filter( 'woocommerce_account_orders_columns', [ $this, 'add_koin_order_columns' ] );
            add_action( 'woocommerce_my_account_my_orders_column_koin-installments', [ $this, 'koin_installments_column_item'] );

            new RenderKoinOrders( $current_page );
        }
    }

    /**
     * Add Koin actions columns
     * @since 1.0.0
     * @param array $columns
     * @return array
     */
    public function add_koin_order_columns( $columns )
    {
        $columns = [
            'order-number'      => __( 'Order', 'wc-koin-official' ),
            'order-status'      => __( 'Status', 'wc-koin-official' ),
            'order-date'        => __( 'Date', 'wc-koin-official' ),
            'koin-installments' => __( 'Installments', 'wc-koin-official' ),
            'order-total'       => __( 'Total', 'wc-koin-official' ),
            'order-actions'     => __( 'Details', 'wc-koin-official' )
        ];

        return $columns;
    }


    /**
     * Render column content
     * @since 1.0.0
     * @param object $order
     * @return void
     */
    public function koin_installments_column_item( $order )
    {
        $postmeta = new PostMeta;

        $order_id = $order->get_id();
        $meta     = $postmeta->get( $order_id, '_installments' );

        $installments = __( "Undefined", 'wc-koin-official' );
        if ( $meta ) {
            $installments = $meta;
        }

        echo esc_html( $installments );
    }
}
