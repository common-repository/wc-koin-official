<?php

namespace WKO\Controllers;

use WKO\Controllers\Actions\CancellOrder;
use WKO\Helpers\Utils;

/**
 * Name: Gateways
 * Create customs status
 * @package Controllers
 * @since 1.0.0
 */
class Status
{
	public function __construct()
	{
        $this->register_koin_custom_statuses();

        add_filter( 'wc_order_statuses', [ $this, 'add_koin_custom_statuses' ], 10, 1 );
        add_action( 'woocommerce_order_status_changed', [ $this, 'on_koin_status_changed' ], 10, 3 );
	}

    /**
     * Controller Koin statuses
     * @since 1.0.0
     * @return array
     */
    private function get_koin_status()
    {
        return [
            'wc-awaiting-payment'  => __( 'Awaiting payment', 'wc-koin-official' ),
            'wc-awaiting-analysis' => __( 'Under analysist', 'wc-koin-official' )
        ];
    }

    /**
     * Register Koin custom statuses
     * @since 1.0.0
     * @return void
     */
    public function register_koin_custom_statuses()
    {
        $statuses = $this->get_koin_status();

        foreach( $statuses as $key => $status ) {
            register_post_status( $key, [
                'label'                     => $status ,
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
            ] );
        }
    }

    /**
     * Add Koin custom statuses
     * @since 1.0.0
     * @param array $order_statuses
     * @return array
     */
    public function add_koin_custom_statuses( $order_statuses ) {
        $statuses = $this->get_koin_status();

        foreach( $statuses as $key => $status ) {
            if (!isset($order_statuses[$key])) {
                $order_statuses[$key] = $status;
            }
        }

        return $order_statuses;
    }

    /**
     * Call koin cancellation method
     * @since 1.0.0
     * @param int $id
     * @param string $from
     * @param string $to
     * @return void
     */
    public function on_koin_status_changed( $id, $from, $to )
    {
        $order = wc_get_order( $id );

        $payment_method  = $order->get_payment_method();
        $payment_methods = Utils::koin_payment_methods();

        if ( array_intersect( $payment_methods, [ $payment_method ] ) ) {
            if ( $to === 'cancelled' ) {
                new CancellOrder( $id );
            }
        }
    }

}
