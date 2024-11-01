<?php

namespace WKO\Controllers\Gateways\Blocks;

use WKO\Controllers\Render\CreditCheckout;
use WKO\Helpers\Config;

/**
 * Dummy Payments Blocks integration
 *
 * @since 1.0.3
 */
final class Credit extends AbstractBlockGateway
{

    protected $name = 'wc-koin-credit';

    public function getGatewayCustomFields(): array
    {
        $credit = new CreditCheckout();

        return apply_filters('wc-koin-official_credit_block_checkout_fields', [
            'brand'     => esc_url(Config::__image('icons/brands/')),
            'installments' => $credit->get_installments(),
            'orgId'       => $credit->get_org_id()
        ]);
    }

}
