<?php

namespace WKO\Controllers;

use WKO\Controllers\Render\Notification as RenderNotification;

/**
 * Name: Notifications
 * Handle admin notifications
 * @package Controllers\Render
 * @since 1.0.0
 */
class Notifications
{
	private $data;
	
	/**
	 * Class Constructor
	 * @since 1.0.0
	 * @param string $title
	 * @param string $message
	 * @param string $type
	 * @param string $is_html
	 * @param string $dismissible
	 */
	public function __construct( $title, $message, $type, $is_html = false, $dismissible = false )
	{
		$this->data = [
			'message'     => $message, 
			'type'        => $type,
			'is_html'     => $is_html,
			'dismissible' => $dismissible,
			'strong'	  => $title
		];
		
		$this->set_notice_class();

        add_action( 'admin_notices', [ $this, 'handle_notifications' ] );
	}

	/**
	 * Set notice class by type
	 * @since 1.0.0
	 * @return void
	 */
	private function set_notice_class()
	{
		$type = $this->data['type'];

		switch ( $type ) {
			case 'error':
				$class = "notice notice-error";
				break;

			case 'warnning':
				$class = "notice notice-warning";
				break;

			case 'info':
				$class = "notice notice-error";
				break;

			case 'success':
				$class = "notice notice-info";
				break;
			
			default:
				$class = "notice notice-info";
				break;
		}

		$this->data['class'] = $class;
	}

	/**
	 * Call notification render class
	 * @since 1.0.0
	 * @return RenderNotification
	 */
	public function handle_notifications()
	{
		return new RenderNotification( $this->data );
	}
    
	
}