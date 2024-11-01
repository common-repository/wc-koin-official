<?php

namespace WKO\Controllers\Gateways\Blocks;

final class Pix extends AbstractBlockGateway
{
    protected $name = 'wc-koin-pix';

    public function getGatewayCustomFields(): array
    {
        return apply_filters('wc-koin-official_pix_block_checkout_fields', []);
    }
}
