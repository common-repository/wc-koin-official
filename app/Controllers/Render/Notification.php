<?php

namespace WKO\Controllers\Render;

use WKO\Controllers\Render;

/**
 * Name: Notifications
 * Handle admin notifications
 * @package Controllers\Render
 * @since 1.0.0
 */
class Notification extends Render
{
	/**
	 * Class Constructor
	 * @param array $notification
	 */
	public function __construct( $notification )
	{
		if ( ! empty( $notification ) ) {
			$this->render( '/notification.php', $notification  );
		}
	}
	
}