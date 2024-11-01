<?php

namespace WKO\Controllers\Gateways;

/**
 * Name: Interface Gateways
 * Interface for requests
 * @package Controllers\Webhooks
 * @since 1.0.0
 */
interface InterfaceGateways
{
    public function init_form_fields();
    public function payment_fields();
    public function validate_fields();
    public function process_payment( $order_id );
}