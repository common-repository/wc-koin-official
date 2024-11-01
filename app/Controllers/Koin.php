<?php

namespace WKO\Controllers;

use WKO\Model\PostMeta;
use WKO\Services\Koin\Requests\Orders\Cancel;
use WKO\Services\Koin\Requests\Orders\Capture;
use WKO\Services\Koin\Requests\Orders\Create;
use WKO\Services\Koin\Requests\Orders\Get;

/**
 * Name: Koin
 * Koin controller
 * @package Controllers
 * @since 1.0.0
 */
class Koin 
{
    private $meta;
    private $logs;

	public function __construct()
	{
        $this->meta = new PostMeta;
        $this->logs = new Logs;
	}

    /**
     * Handle get order request
     * @since 1.0.0
     * @param int $order_id
     * @param int|bool $order_id
     * @return array
     */
    public function get_order( $order_id, $koin_order_id = false )
    {
        if ( ! $koin_order_id ) {
            $koin_order_id = $this->meta->get( $order_id, 'koin_order_id' );

            if ( ! $koin_order_id || ! intval( $koin_order_id ) ) {
                return [
                    'success'   => false,
                    'message' => __( 'Could not fetch order_id from database.', 'wc-koin-official' )
                ];
            }
        }

        $koin = new Get( $koin_order_id );
        $response = $koin->handle_request();

        if ( is_wp_error( $response ) || ! $response ) {

            $message = __( 'Error when making the request', 'wc-koin-official' );

            $this->logs->get_order_error( $message, $response );

            return [
                'error'   => true,
                'message' => $message
            ];
        }

        return $response;
    }

    /**
     * Handle create order request
     * @since 1.0.0
     * @param array $body
     * @return array
     */
    public function create_order( $body )
    {
        $koin = new Create( $body );
        $response = $koin->handle_request();

        if ( is_wp_error( $response ) || ! $response ) {

            $message = __( 'Error when making the request', 'wc-koin-official' );

            $this->logs->create_order_error( $message, $response );

            return [
                'error'   => true,
                'message' => $message
            ];
        }

        return $response;
    }

    /**
     * 
     */
    public function capture_order($order_id)
    {
        $request = new Capture($order_id);
        $response = $request->handle_request();

        if (!is_wp_error( $response )) {
            return $response;
        }

        return [
            'error'    => true,
            'message'  => 'Error capturing order',
            'response' => $response
        ];
    }

    /**
     * Handle cancel order request
     * @since 1.0.0
     * @param int $order_id
     * @param int|bool $order_id
     * @return array
     */
    public function cancel_order( $order_id, $koin_order_id )
    {
        if ( ! $order_id || ! $koin_order_id ) {
            return [
                'error'   => true,
                'message' => __( 'Could not fetch order_id from database.', 'wc-koin-official' )
            ];
        }

        $koin = new Cancel( $koin_order_id );
        $response = $koin->handle_request();

        return $response;
    }
}