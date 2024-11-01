<?php

namespace WKO\Controllers;

use WKO\Model\Options;
use WKO\Controllers\Webhooks\Order;

/**
 * Name: Woocommerce
 * Intance Woocommerce classes
 * @package Controllers
 * @since 1.0.0
 */
class Webhooks 
{
	public function __construct()
	{
		$webhook = new Order;
		add_action( 'woocommerce_api_koin_orders_' . $this->get_token('orders'), [ $webhook, 'handle_notifications' ] );
	}
	/**
	 * Get webhook endpoint
	 * @since 1.0.0
	 * @return string
	 */
	private function get_token( $type )
	{
		$opt   = new Options;
		$token = $opt->get( "_webhook_token_$type" );
		
		if ( ! $token ) {
			$bytes = random_bytes(15);
			$token = bin2hex( $bytes );
			
			$opt->update( "_webhook_token_$type", $token );
		}
		
		return $token;
	}
	
}