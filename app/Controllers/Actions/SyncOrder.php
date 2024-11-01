<?php

namespace WKO\Controllers\Actions;

use WKO\Controllers\Koin;
use WKO\Controllers\Logs;
use WKO\Model\Options;

/**
 * Name: Sync Order
 * Sync Order
 * @package Controllers\Actions
 * @param array $vars
 * @since 1.0.0
 */
class SyncOrder
{
    private $order_id;
    private $koin_order_id;

    public function __construct( $vars )
    {

        if ( ! $this->verify_vars( $vars ) ) {
            return wp_send_json([
                'message' => 'missign params',
                'success' => false
            ], 400);
        }

        return $this->sync_koin_order();
    }

    /**
     * Verify request vars
     * @since 1.0.0
     * @param array $vars
     * @return bool
     */
    public function verify_vars( $vars )
    {
        if ( ! isset( $vars["order_id"] ) || ! $vars["koin_order_id"] ) {
            return false;
        }

        $this->order_id = intval( $vars["order_id"] ) ? $vars["order_id"] : 0;
        $this->koin_order_id = intval( $vars["koin_order_id"] ) ? $vars["koin_order_id"] : 0;

        return true;
    }

    /**
     * Sync Koin order
     * @since 1.0.0
     * @return mixed
     */
    public function sync_koin_order()
    {
        $koin = new Koin;
        $logs = new Logs;

        $response = $koin->get_order( $this->order_id, $this->koin_order_id );

        if ( isset( $response['body'] ) ) {

            $body = json_decode( $response['body'] );

            $wc_order = wc_get_order( $this->order_id );

            if ( $body->code === 404 ) {

                $wc_order->add_order_note( sprintf( "<strong>%s</strong> : %s",
                    WKO_PLUGIN_NAME,
                        __( "Synchronization failed. Unable to find the order on Koin.", 'wc-koin-official' )
                ), true );

                $title   = __( "KOIN SYNC SUCCESS", 'wc-koin-official' );
                $body_request = "\nBody request:\n" . print_r( $body, true ) . "\n\n";
                $message = sprintf( "%s %s",
                    __( "Synchronization failed. Unable to find the order on Koin.", 'wc-koin-official' ),
                    $body_request
                );

                $logs->sync_notice_error( $title, $message );

            } else {

                if ( $body->status ) {

                    $status = $body->status;
                    $order_status = $this->translate_status( $status->type );

                    if ( $order_status ) {

                        $wc_order->update_status( $order_status, sprintf(
                                "<strong>%s</strong> : %s",
                                WKO_PLUGIN_NAME,
                                __( "Status changed automatically by Koin after sync.", 'wc-koin-official' )
                            ),
                        true );

                        $title = __( "KOIN SYNC SUCCESS", 'wc-koin-official' );
                        $body_request = "\nBody request:\n" . print_r( $body, true ) . "\n\n";

                        $message = sprintf( "%s %s",
                            __( "The sync work successfully updated.", 'wc-koin-official' ),
                            $body_request
                        );

                        $logs->sync_notice_success( $title, $message );

                        return wp_send_json([
                            'message' => __( 'The order has been successfully updated.', 'wc-koin-official' ),
                            'success' => true
                        ], 200);
                    }

                } else {

                    $title = __( "KOIN SYNC ERROR", 'wc-koin-official' );
                    $body_request = "\nBody request:\n" . print_r( $body, true ) . "\n\n";

                    $message = sprintf( "%s %s",
                        __( "Sync doesn't work properly!", 'wc-koin-official' ),
                        $body_request
                    );
                    $logs->sync_notice_error( $title, $message );
                }
            }

        } else {

            $title = __( "KOIN SYNC ERROR", 'wc-koin-official' );
            $body_request = "\nBody request:\n" . print_r( $response, true ) . "\n\n";

            $message = sprintf( "%s %s",
                __( "Sync doesn't work properly!", 'wc-koin-official' ),
                $body_request
            );

            $logs->sync_notice_error( $title, $message );
        }

        return wp_send_json([
            'message' => __( "Synchronization failed. Unable to find the order on Koin.", 'wc-koin-official' ),
            'success' => false
        ], 400);

    }

    private function translate_status( $status )
    {
        $order_status = false;
        $success_status = get_option('wc_koin_settings_status');

        if ( ! $success_status ) {
            $success_status = "wc-processing";
        }

        switch ($status) {
            case 'Collected':
                $order_status = $success_status;
                break;
            case 'Waiting':
                $order_status = 'wc-awaiting-payment';
                break;
            case 'Pending':
                $order_status = 'wc-awaiting-analysis';
                break;
            case 'Cancelled':
                $order_status = 'wc-cancelled';
                break;
            case 'Published':
                $order_status = 'wc-on-hold';
                break;
            case 'Opened':
                $status  = 'wc-awaiting-analysis';
                break;
            case 'Failed':
                $order_status = 'wc-failed';
                break;
        }

        return $order_status;
    }
}
