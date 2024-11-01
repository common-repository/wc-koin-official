<?php

namespace WKO\Controllers;

use WC_Logger;

/**
 * Name: Logs
 * Woocommerce logs
 * @package Controllers
 * @since 1.0.0
 */
class Logs
{
    private $wc;

    public function __construct()
    {
      if (class_exists('WC_Logger')) {
        $this->wc = new WC_Logger();
      }
    }

    /**
     * Create order messages
     * @since 1.0.0
     * @param string $title
     * @param string $var
     * @return void
     */
    public function create_order( $title, $var )
    {
      if (isset($this->wc)) {
        $prefix = WKO_PLUGIN_SLUG . "-create-order";
        $this->wc->add( $prefix, "{$title} : ".print_r( $var, true ) );
      }
    }

    /**
     * Get order error messages
     * @since 1.0.0
     * @param string $title
     * @param string $var
     * @return void
     */
    public function get_order_error( $title, $var )
    {
      if (isset($this->wc)) {
        $prefix = WKO_PLUGIN_SLUG . "-get-order-error-";
        $this->wc->add( $prefix, "{$title} : ".print_r( $var, true ) );
      }
    }

    /**
     * Cancel order error messages
     * @since 1.0.0
     * @param string $title
     * @param string $var
     * @return void
     */
    public function cancel_order_error( $title, $var )
    {
      if (isset($this->wc)) {
        $prefix = WKO_PLUGIN_SLUG . "-cancel-order-error-";
        $this->wc->add( $prefix, "{$title} : ".print_r( $var, true ) );
      }
    }


    /**
     * Webhook messages
     * @since 1.0.0
     * @param string $title
     * @param string $var
     * @return void
     */
    public function webhook_message( $title, $var )
    {
      if (isset($this->wc)) {
        $prefix = WKO_PLUGIN_SLUG . "-webhook-message-";
        $this->wc->add( $prefix, "{$title} : ".print_r( $var, true ) );
      }
    }

    /**
     * Sync notice error messages
     * @since 1.0.0
     * @param string $title
     * @param string $var
     * @return void
     */
    public function sync_notice_error( $title, $var )
    {
      if (isset($this->wc)) {
        $prefix = WKO_PLUGIN_SLUG . "-sync-notice-error-";
        $this->wc->add( $prefix, "{$title} : ".print_r( $var, true ) );
      }
    }

    /**
     * Get order success messages
     * @since 1.0.0
     * @param string $title
     * @param string $var
     * @return void
     */
    public function get_order_success( $title, $var )
    {
      if (isset($this->wc)) {
        $prefix = WKO_PLUGIN_SLUG . "-get-order-success-";
        $this->wc->add( $prefix, "{$title} : ".print_r( $var, true ) );
      }
    }

    /**
     * Cancel order success messages
     * @since 1.0.0
     * @param string $title
     * @param string $var
     * @return void
     */
    public function cancel_order_success( $title, $var )
    {
      if (isset($this->wc)) {
        $prefix = WKO_PLUGIN_SLUG . "-cancel-order-success-";
        $this->wc->add( $prefix, "{$title} : ".print_r( $var, true ) );
      }
    }


    /**
     * Sync notice success messages
     * @since 1.0.0
     * @param string $title
     * @param string $var
     * @return void
     */
    public function sync_notice_success( $title, $var )
    {
      if (isset($this->wc)) {
        $prefix = WKO_PLUGIN_SLUG . "-sync-notice-success-";
        $this->wc->add( $prefix, "{$title} : ".print_r( $var, true ) );
      }
    }

    /**
     * Log all requests
     * @since 1.0.0
     * @param string $title
     * @param string $var
     * @return void
     */
    public function set_request_logs( $title, $var )
    {
      if (isset($this->wc)) {
        $prefix = WKO_PLUGIN_SLUG . "-request-response-logs-";
        $this->wc->add( $prefix, "{$title} : ".print_r( $var, true ) );
      }
    }

}
