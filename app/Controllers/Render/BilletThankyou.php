<?php

namespace WKO\Controllers\Render;

use WKO\Controllers\Render;
use WKO\Helpers\Config;
use WKO\Model\PostMeta;

/**
 * Render the billet thankyou page
 *
 * @package Controller\Render
 * @since 1.0.0
 */
class BilletThankyou extends Render
{
    private $vars;
    public function __construct()
    {
        $this->request();
    }

    private function get_order()
    {
        $this->vars = [];
        $payment_link = "";

        $key = sanitize_text_field( $_REQUEST['key'] );
        if ( $key ) {
            $order = wc_get_order_id_by_order_key( $key );

            $post_m = new PostMeta;
            $payment_link = $post_m->get($order, '_payment_link');
        }

        $blank_link = get_post_meta($order, '_koin_blank_link', true);

        $this->vars = [
            'payment_link' => $payment_link,
            'blank_link'   => !$blank_link,
            'logo'         => Config::__image( 'koin/b-koin-258.png' )
        ];
        update_post_meta($order, '_koin_blank_link', true);
    }

    private function request()
    {
        $this->get_order();
        $this->render( 'templates/thankyou-page/billet.php', $this->vars );
    }
}
