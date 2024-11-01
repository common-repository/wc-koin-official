<?php

namespace WKO\Controllers\Render;

use WKO\Controllers\Render;
use WKO\Helpers\Config;
use WKO\Model\PostMeta;

/**
 * Render the credit thankyou page
 * 
 * @package Controller\Render
 * @since 1.0.0
 */
class CreditThankyou extends Render
{
    private $vars;
    public function __construct()
    {
        $this->vars = [];
        $this->request();
    }

    private function get_order()
    {
        $key = sanitize_text_field( $_REQUEST['key'] );

        if ( $key ) {
            $order = wc_get_order_id_by_order_key( $key );
            $this->get_metas($order);
        }
    }

    private function get_metas($order)
    {
        $metas = [
            'installments',
            'order_total'
        ];

        $model = new PostMeta;

        foreach ($metas as $key) {
            $this->vars[$key] = $model->get($order, "_$key");
        }
    }

    private function get_logo()
    {
        $this->vars['logo'] = Config::__image( 'koin/b-koin-258.png' );
    }

    private function request()
    {
        $this->get_order();
        $this->get_logo();
        
        $this->render( 'templates/thankyou-page/credit.php', $this->vars );
    }
}