<?php

namespace WKO\Controllers\Gateways\Blocks;


final class Billet extends AbstractBlockGateway
{
    protected $name = 'wc-koin-billet';

    public function getGatewayCustomFields(): array
    {
        return apply_filters('wc-koin-official_billet_block_checkout_fields', []);
    }
}
