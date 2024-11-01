<?php

namespace WKO\Controllers\Render;

use WKO\Controllers\Render;
use WKO\Helpers\Config;

/**
 * Name: Billet Checkout
 * Render the billet checkout fields
 * @package Controller\Render
 * @since 1.0.0
 */
class BilletCheckout extends Render
{
    private $vars;

    public function __construct()
    {
        $this->get_vars();
        $this->render( 'templates/checkout/billet.php', $this->vars );
    }

    private function get_vars()
    {
        $this->vars = [
            'banner_info' => Config::__image( 'koin/banner-koin.png' )
        ];
    }
}
