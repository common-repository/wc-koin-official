<?php

namespace WKO\Controllers\Actions;

use WKO\Controllers\Koin;
use WKO\Controllers\Logs;
use WKO\Model\PostMeta;

/**
 * Name: Cancell Order
 * Cancell Order
 * @package Controllers\Actions
 * @param array $vars
 * @since 1.0.0
 */
class CancellOrder
{
    private $order_id;
    private $koin_order_id;

    public function __construct( $order_id )
    {
        $post_m = new PostMeta;

        $this->koin_order_id = $post_m->get( $order_id, '_order_id' );
        $this->order_id = $order_id;

        if ($this->order_id && $this->koin_order_id ) {
            $this->cancel_koin_order();
        }
    }

    /**
     * Sync Koin order
     * @since 1.0.0
     * @return mixed
     */
    public function cancel_koin_order()
    {
        $koin = new Koin;
        $logs = new Logs;

        $response = $koin->cancel_order( $this->order_id, $this->koin_order_id );
        $wc_order = wc_get_order( $this->order_id );

        if ( isset( $response['body'] ) ) {

            $body = json_decode( $response['body'] );

            if ( isset( $body->status ) ) {

                $status       = $body->status;
                $order_status = $status->type;

                if ( $order_status ===  'Cancelled' ) {

                    $title = __( "KOIN CANCELLATION SUCCESS", 'wc-koin-official' );
                    $body_request = "\nBody request:\n" . print_r( $body, true ) . "\n\n";

                    $message = sprintf( "%s %s",
                        __( "The cancellation work successfully.", 'wc-koin-official' ),
                        $body_request
                    );

                    $wc_order->add_order_note( sprintf( "<strong>%s</strong> : %s",
                        WKO_PLUGIN_NAME,
                        __( "The cancellation work successfully.", 'wc-koin-official' )
                    ), true );

                } else {

                    $title   = __( "KOIN CANCELLATION ERROR", 'wc-koin-official' );

                    $message = sprintf( "%s %s",
                        __( "Cancellation failed. Unable to find the order on Koin.", 'wc-koin-official' ),
                        "\nBody request:\n" . print_r( $body, true ) . "\n\n"
                    );

                    $wc_order->add_order_note( sprintf( "<strong>%s</strong> : %s",
                        WKO_PLUGIN_NAME,
                        __( "Cancellation doesn't work properly!", 'wc-koin-official' )
                    ), true );
                }

            } else {

                $title   = __( "KOIN CANCELLATION ERROR", 'wc-koin-official' );
                $body_request = "\nBody request:\n" . json_encode( $body ) . "\n\n";

                $message = sprintf( "%s %s",
                    __( "Cancellation failed. Unable to find the order on Koin.", 'wc-koin-official' ),
                    $body_request
                );

                $wc_order->add_order_note( sprintf( "<strong>%s</strong> : %s",
                    WKO_PLUGIN_NAME,
                    __( "Cancellation doesn't work properly!", 'wc-koin-official' )
                ), true );
            }

        } else {

            $title = __( "KOIN CANCELLATION ERROR", 'wc-koin-official' );

            $message = sprintf( "%s %s",
                __( "Cancellation doesn't work properly!", 'wc-koin-official' ),
                "\nBody request:\n" . print_r( $response, true ) . "\n\n"
            );

            $wc_order->add_order_note( sprintf( "<strong>%s</strong> : %s",
                WKO_PLUGIN_NAME,
                __( "Cancellation doesn't work properly!", 'wc-koin-official' )
            ), true );

        }

        $logs->cancel_order_success( $title, $message );
    }
}
