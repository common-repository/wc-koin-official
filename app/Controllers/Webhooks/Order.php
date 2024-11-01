<?php

namespace WKO\Controllers\Webhooks;

use WKO\Controllers\Koin;
use WKO\Controllers\Logs;
use WKO\Model\PostMeta;

/**
 * Handle Order Webhooks
 *
 * @package Controllers\Webhooks
 * @since 1.0.0
 */
class Order implements InterfaceWebhooks
{
    private $logger;
    private $postmeta;
    private $metas;
    private $wc_order_id;

    public function __construct()
    {
        $this->logger   = new Logs;
        $this->postmeta = new PostMeta;
    }

    /**
     * Receive webhook notifications
     * @since 1.0.0
     * @return void
     */
    public function handle_notifications()
    {
        $post_data = file_get_contents( 'php://input' );

		$body = empty( $post_data ) ? false : json_decode( $post_data );
        $metas = [];

        if ( $body && is_object( $body ) ) {

            if ( isset( $body->status ) ) {

                $status = $body->status;
                $order_status = isset( $status->type ) ? $status->type : false;

                $statuses = [
                    'Collected',
                    'Cancelled',
                    'Pending',
                    'Waiting',
                    'Failed',
                    'Authorized'
                ];

                if ( array_intersect( $statuses, [ $order_status ] ) ) {

                    $transaction  = $body->transaction;
                    $reference_id = isset( $transaction->reference_id ) ? $transaction->reference_id : false;

                    $reference_id = explode( "_", $reference_id );

                    if ( ! empty( $reference_id ) && is_array( $reference_id ) ) {
                        $reference_id = $reference_id[ count($reference_id) - 1 ];
                    } else {
                        $reference_id = false;
                    }

                    $this->wc_order_id = intval( $reference_id ) ? $reference_id : false;

                    if ($order_status === 'Authorized' && isset( $body->order_id )) {
                        $this->capture_order($body->order_id);
                    }

                    $this->handle_statuses( $order_status );

                    $title = sprintf( "==== KOIN WEBHOOK SUCCESS ====\n- %s",
                        __( "Notification received successfully:", 'wc-koin-official' )
                    );

                    $this->logger->webhook_message( $title, $body );

                } else {
                    $title = sprintf( "==== KOIN WEBHOOK ERROR ====\n- %s",
                        __( "Notification status invalid:", 'wc-koin-official' )
                    );

                    $this->logger->webhook_message( $title, $body );
                }
            }
        }
    }

    /**
     * Capture authorized order
     *
     * @since 1.2.0
     * @param string $order_id
     * @return void
     */
    private function capture_order($order_id)
    {
        $koin = new Koin;
        $response = $koin->capture_order($order_id);

        if (!isset($response['error']) && isset($response['body'])) {
            $body = json_decode( $response['body'] );

            if ( isset( $body->status->type ) ) {

                $this->handle_statuses($body->status->type);

                $this->logger->webhook_message(
                    sprintf(
                        "==== KOIN WEBHOOK CAPTURED SUCCESS ====\n- %s",
                        __( 'Order captured:', 'wc-koin-official' )
                    ),
                    $response
                );

                return;
            }

        }

        $this->logger->webhook_message(
            sprintf(
                "==== KOIN WEBHOOK CAPTURED ERROR ====\n- %s",
                $response['message']
            ),
            $response['response']
        );
    }

    /**
     * Update order status
     *
     * @since 1.0.0
     * @param string $status
     * @return void
     */
    private function handle_statuses( $status )
    {
        $order_status = false;
        $success      = get_option('wc_koin_settings_status');

        if ( ! $success ) {
            $success = "wc-processing";
        }

        switch ( $status ) {
            case 'Collected':
                $order_status = $success;
                $message = __( "Order completed successfully!", 'wc-koin-official' );
                break;

            case 'Waiting':
                $order_status = 'wc-awaiting-payment';
                $message = __( "Waiting for first payment.", 'wc-koin-official' );
                break;

            case 'Pending':
                $order_status = 'wc-awaiting-analysis';
                $message = __( "Awaiting credit analysis by Koin.", 'wc-koin-official' );
                break;

            case 'Authorized':
                $status = 'wc-awaiting-analysis';
                $message = __( "Awaiting credit analysis by Koin.", 'wc-koin-official' );
                break;

            case 'Cancelled':
                $order_status = 'wc-cancelled';
                $message = __( "Order cancelled successfully!", 'wc-koin-official' );
                break;

            case 'Published':
                $order_status = 'wc-on-hold';
                $message = __( "Order Published!", 'wc-koin-official' );
                break;

            case 'Opened':
                $status  = 'wc-awaiting-analysis';
                $message = __( "Order Opened!", 'wc-koin-official' );
                break;

            case 'Failed':
                $order_status = 'wc-failed';
                $message = __( "Order failed!", 'wc-koin-official' );
                break;
        }


        if ( $order_status ) {
            $wc_order = wc_get_order( $this->wc_order_id );
            $wc_order->update_status( $order_status, sprintf( "<strong>%s</strong> : %s -  %s",
                    WKO_PLUGIN_NAME,
                    __( "Status changed automatically by Koin", 'wc-koin-official' ),
                    $message
                ),
                true
            );
        }
    }

    /**
     * Validate order metas
     *
     * @since 1.0.0
     * @param object $body
     * @return void
     */
    private function validate_metas( $body )
    {
        $failed = false;

        $metas = [
            "_order_id",
            "_installments",
        ];

        foreach ( $metas as $meta ) {

            if ( ! isset( $this->metas[ $meta ] ) ) {

                $failed = true;

                $title = sprintf( "==== KOIN WEBHOOK ERROR ====\n- %s %s %s",
                    __( "It was not possible to identify the ", 'wc-koin-official' ),
                    $meta,
                    __( " field in the received notification.", 'wc-koin-official' )
                );

                $this->logger->webhook_message( $title, $body);
            }
        }

        if ( ! $failed ) {
            $this->save_metas( $body );
            return true;
        }

        return false;
    }

    /**
     * Save order metas
     *
     * @since 1.0.0
     * @param object $body
     * @return void
     */
    private function save_metas( $body )
    {
        $metas = [
            "_order_id",
            "_installments",
        ];

        $order = wc_get_order( $this->wc_order_id );

        if ( $order ) {
            foreach ( $this->metas as $key => $meta ) {
                if ( array_intersect( $metas, [ $key ] ) ) {
                    $this->postmeta->update( $this->wc_order_id, $key, $meta );
                }
            }

        } else {
            $title = sprintf( "==== KOIN WEBHOOK ERROR ====\n- %s",
                __( "Woocomerce orde not found:", 'wc-koin-official' )
            );

            $this->logger->webhook_message( $title, [
                'order_id' => $this->wc_order_id,
                'body'     => $body
            ] );
        }
    }
}
