<?php

namespace WKO\Controllers;

use WKO\Controllers\Gateways\Billet;
use WKO\Controllers\Gateways\Blocks\Billet as BlocksBillet;
use WKO\Controllers\Gateways\Blocks\Credit as BlocksCredit;
use WKO\Controllers\Gateways\Blocks\Pix as BlocksPix;
use WKO\Controllers\Gateways\Credit;
use WKO\Controllers\Gateways\Pix;
use WKO\Controllers\Orders\EditOrders;
use WKO\Controllers\Orders\KoinOrders;
use WKO\Controllers\Orders\ViewOrders;

/**
 * Name: Woocommerce
 * Intance Woocommerce classes
 * @package Controllers
 * @since 1.0.0
 */
class Woocommerce
{
    public function initialize()
    {
		$this->instance_main_controllers();
		$this->instance_order_controllers();

        add_action(
            'woocommerce_store_api_checkout_update_order_from_request',
            [PersonType::class, 'updateBlockOrderMeta'],
            10,
            2
        );
    }

    public function registerGatewayUpdateTotal()
    {
        if (function_exists( 'woocommerce_store_api_register_update_callback')) {
            woocommerce_store_api_register_update_callback(
                array(
                    'namespace' => 'wc-koin-gateway-update-total',
                    'callback'  => [$this, 'gatewayUpdateTotal'],
                )
            );
        }
    }

    public function gatewayUpdateTotal($data)
    {
        WC()->session->set('chosen_payment_method', $data['payment_method']);
        WC()->cart->calculate_totals();
    }

	public function loadWooCommerceBlocks(): void
    {
        add_action('woocommerce_blocks_checkout_block_registration', [$this, 'registerCustomWooCommerceBlocks'], 10, 1);
        add_action('woocommerce_blocks_payment_method_type_registration', [$this, 'registerBlockGateway'], 10, 1);
    }

	public function registerPaymentGateways(array $gateways): array
    {
        $gateways[] = Billet::class;
        $gateways[] = Pix::class;
        $gateways[] = Credit::class;

        return $gateways;
    }

	public function registerCustomWooCommerceBlocks($integration_registry): void
    {
        $integration_registry->register(new PersonType());
    }

	public function registerBlockGateway(object $paymentMethodRegistry): void
    {
        $paymentMethodRegistry->register(new BlocksPix());
        $paymentMethodRegistry->register(new BlocksBillet());
        $paymentMethodRegistry->register(new BlocksCredit());
    }

	/**
	 * Call main controllers classes
	 * @since 1.0.0
	 * @return void
	 */
	private function instance_main_controllers()
	{
		new Webhooks;
		new Settings;
        new Status;
	}

	/**
	 * Call order controllers classes
	 * @since 1.0.0
	 * @return void
	 */
    private function instance_order_controllers()
    {
        new EditOrders;
        new KoinOrders;
        new ViewOrders;
    }
}
