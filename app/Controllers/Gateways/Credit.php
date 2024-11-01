<?php

namespace WKO\Controllers\Gateways;

use Exception;
use WKO\Controllers\Render\CreditCheckout;
use WKO\Controllers\Render\CreditThankyou;
use WKO\Helpers\Config;
use WKO\Services\Koin\Requests\Card\Tokenize;
use WC_Order_Item_Fee;
use WC_Order;

/**
 * Name: Credit
 * Structure the credit card payment method
 * @package Controllers\Gateways
 * @since 1.0.0
 */
class Credit extends Gateway implements InterfaceGateways
{
    public function __construct() {

        $this->id                 = "wc-koin-credit";
        $this->payment_method     = "credit";
        $this->has_fields         = false;
        $this->method_title       = __( "Koin - Pay with Credit Card", 'wc-koin-official' );
        $this->method_description = sprintf( "<div class='description'>%s <a target='_blank' href='%s'>%s<i class='fa-solid fa-up-right-from-square'></i></a></div>",
                                        __( "Pay in using credit card  payment method!", 'wc-koin-official' ),
                                        esc_url( "https://www.koin.com.br/#contato" ),
                                        __( "Create your koin account.", 'wc-koin-official' ),
                                    );

        $this->supports = [
            "products"
        ];

        $this->init_form_fields();

        $this->init_settings();

        $this->title       = $this->get_option( "title" );
        $this->description = $this->get_option( "description" );
        $this->enabled     = $this->get_option( "enabled" );

        add_action( 'woocommerce_thankyou_' . $this->id, [ $this, 'show_thankyou_page' ]);

        if ( is_admin() ) {
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
        }

        add_filter( 'wc_koin_transaction_data', [ $this, 'setInstallmentFee' ], 10, 3 );
        add_action( 'wc_koin_official-after_success_order', [ $this, 'setInstallmentTaxe' ], 10, 1 );

        parent::__construct();
    }

    /**
     * Create/Edit gateway options
     * @since 1.0.0
     * @return void
     */
    public function init_form_fields()
    {

        $this->form_fields = [
            "enabled" => [
                "title"       => __( 'Enable', 'wc-koin-official' ),
                "label"       => __( 'Enable payment with Credit Card', 'wc-koin-official' ),
                "type"        => "checkbox",
                "description" => __( 'Check this option to activate the Koin payment method', 'wc-koin-official' ),
                "default"     => 'no',
                "desc_tip"    => true
            ],

            "title" => [
                "title"       => __( 'Title', 'wc-koin-official' ),
                "type"        => 'text',
                "description" => __( 'This controls the title which the user sees during checkout.', 'wc-koin-official' ),
                "default"     => __( 'Credit Card', 'wc-koin-official' ),
                "desc_tip"    => true
            ],

            "description" => [
                "title"       => __( 'Description', 'wc-koin-official' ),
                "type"        => 'textarea',
                "description" => __( 'This controls the description which the user sees during checkout.', 'wc-koin-official' ),
                "default"     => __( 'Pay using Credit Card payment method!', 'wc-koin-official' ),
                "desc_tip"    => true
            ],

            "installments" => [
                "title"       => __( 'Installments quantity', 'wc-koin-official' ),
                "type"        => 'number',
                "description" => __( 'This controls the quantity of installments for credit card.', 'wc-koin-official' ),
                "default"     => 12,
                "desc_tip"    => true
            ],

            "discount_type" => [
				'title'       => __('Discount type', 'wc-koin-official'),
				'type'        => 'select',
				'description' => __('In this option you can define whether the discount will be calculated by percentage (%) or by a fixed amount.', 'wc-koin-official' ),
				'desc_tip'    => true,
                'class'       => 'wko-discount-type',
				'options'     => [
					0 => __('Disabled', 'wc-koin-official'),
					1 => __('Percentage (%)', 'wc-koin-official'),
					2 => __('Fixed value (R$)', 'wc-koin-official')
				],
				'default'     => 0,
            ],

            "discount_value" => [
				'title'       => __('Discount value', 'wc-koin-official' ),
				'type'        => 'text',
                'class'       => 'wko-discount-value',
				'description' => __('In this option you can define the discount amount.', 'wc-koin-official' ),
				'desc_tip'    => true,
				'default'     => '0',
				'placeholder' => '0',
            ],

            "rate_transfer_type" => [
				'title'       => __('Rate transfer type', 'wc-koin-official'),
				'type'        => 'select',
				'description' => __('Select the credit card interest charging type (simples or By installment)', 'wc-koin-official' ),
				'desc_tip'    => true,
                'class'       => 'wko-rate-transfer-type',
				'options'     => [
					0 => __('Disabled', 'wc-koin-official'),
					1 => __('Simple', 'wc-koin-official'),
					2 => __('By installment', 'wc-koin-official')
				],
				'default'     => 0,
            ],

            "rate_transfer_format" => [
				'title'       => __('Rate transfer format', 'wc-koin-official'),
				'type'        => 'select',
				'description' => __('Select the credit card interest charging format (percentage or fixed rate)', 'wc-koin-official' ),
				'desc_tip'    => true,
                'class'       => 'wko-rate-transfer-format',
				'options'     => [
					1 => __('Percentage (%)', 'wc-koin-official'),
					2 => __('Fixed value (R$)', 'wc-koin-official')
				],
				'default'     => 0,
            ],

            "rate_transfer_simple" => [
				'title'       => __('Rate value', 'wc-koin-official' ),
				'type'        => 'text',
                'class'       => 'wko-rate-simple-value',
				'description' => __('In this option you can define the rate value.', 'wc-koin-official' ),
				'desc_tip'    => true,
				'default'     => '0',
				'placeholder' => '0',
            ],

            'installments_show_mode' => [
                'title' => __('Installment view', 'wc-koin-official'),
                'type' => 'select',
                'description' => __('Define how installments are displayed at checkout.', 'wc-koin-official'),
                'default' => 'price_total',
                'options' => [
                    'price' => __('12 x R$10,00', 'wc-koin-official'),
                    'price_text' => __('12 x R$10,00 - Sem juros', 'wc-koin-official'),
                    'price_total' => __('12 x R$10,00 (R$120,00)', 'wc-koin-official'),
                    'price_total_text' => __('12 x R$10,00 (R$120,00) - Sem juros', 'wc-koin-official'),
                ]
            ],

            "rate_transfer_by_installments" => [
				'title'       => __('Rate value by installment', 'wc-koin-official' ),
				'type'        => 'text',
                'class'       => 'wko-rate-by-installments-value',
				'description' => __('In this option you can define the fee amount for each installment.', 'wc-koin-official' ),
				'default'     => json_encode([
                    1 => '0.00',
                    2 => '0.00',
                    3 => '0.00',
                    4 => '0.00',
                    5 => '0.00',
                    6 => '0.00',
                    7 => '0.00',
                    8 => '0.00',
                    9 => '0.00',
                    10 => '0.00',
                    11 => '0.00',
                    12 => '0.00',
                ]),
            ],

            "koin_logo" => [
                "type"        => 'hidden',
                "default"     => esc_url( Config::__image( 'koin/b-koin.png' ) ),
            ],
        ];

    }

    /**
     * Render the payment fields
     * @since 1.0.0
     * @return void
     */
    public function payment_fields()
    {

        if ( $this->description ) {

            if ( get_option('wc_koin_settings_environment') === 'sandbox' ) {

                $this->description .= __( " Test mode activate! In this mode transactions are not real.", 'wc-koin-official' );
                $this->description  = trim( $this->description );
            }

            echo wpautop( wp_kses_post( $this->description ) );
        }

        $credit = new CreditCheckout;
        $credit->request();
    }

    /**
     * Validate the payment fields
     *
     * @since 1.0.0
     * @return boolean
     */
    public function validate_fields()
    {
        $session = $this->get_post_vars('wko-session');
        $brand = $this->get_post_vars('wko-card-brand');

        if (!$session) {
            $this->abort_payment_process(__('Unable to get customer session!', 'wc-koin-official'));
        }

        if (!$brand) {
            $this->abort_payment_process(__('Unable to identify credit card brand!', 'wc-koin-official'));
        }

        return true;
    }

    private function tokenize_card()
    {
        $card_fields = $this->get_card_fields();
        $tokenize = new Tokenize($card_fields);
        $response = wp_remote_retrieve_body($tokenize->handle_request());
        $response = json_decode($response) ?? false;

        if ($response && isset($response->secure_token)) {
            return $response->secure_token;
        }

        $this->abort_payment_process(__('Could not authorize credit card', 'wc-koin-official'));
    }

    private function get_card_fields()
    {
        $fields = [
            'wko-card-holder_name' => __('Card Owner', 'wc-koin-official'),
            'wko-card-number' => __('Card Number', 'wc-koin-official'),
            'wko-card-expiration_month' => __('Expiry Date', 'wc-koin-official'),
            'wko-card-expiration_year' => __('Expiry Date', 'wc-koin-official'),
            'wko-card-security_code' => __('Card Code', 'wc-koin-official'),
            'wko-card-installments' => __('Card Installments', 'wc-koin-official'),
            'wko-card-brand' => __('Card Brand', 'wc-koin-official')
        ];

        $data = [];

        foreach($fields as $key => $field) {
            $value =  $this->get_post_vars($key);

            if (!$value) {
                throw new Exception("Missing card fields: $field");
            }

            $key = str_replace('wko-card-', '', $key);
            $data[$key] = $value;

            if ($key === 'number') {
                $data[$key] = preg_replace('/\s+/', '', $value);
            }
        }

        return $data;
    }
    /**
     * Get gateway specific data
     *
     * @since 1.2.0
     * @param object $wc_order
     * @return array
     */
    protected function get_payment_method( $wc_order )
    {
        $installments = $this->get_post_vars('wko-card-installments');
        $token = $this->tokenize_card();

        return [
            "code"         => "CARD",
            "secure_token" => $token,
            "installments" => $installments,
        ];
    }

    /**
     * Get custom fields for each gateway
     *
     * @since 1.2.0
     *
     * @param object $wc_order
     * @return array
     */
    protected function get_custom_gateway_fields( $wc_order )
    {
        $ip_address = $this->get_post_vars('wko-ipaddress');
        $session = $this->get_post_vars('wko-session');
        $address = $this->get_address( $wc_order);

        return [
            "session_id" => $session,
            "ip_address" => $ip_address ?? $this->get_customer_ip(),
            "buyer"      => $this->get_payer_data( $wc_order, $address )
        ];
    }

    /**
     * Get gateways metas
     *
     * @since 1.2.0
     *
     * @param object $body
     * @param object $wc_order
     */
    protected function get_metas($body, $wc_order)
    {
        $installments = $this->get_post_vars('wko-card-installments');

        return [
            '_order_id'     => $body->order_id,
            '_installments' => $installments,
            '_order_total'  => $wc_order->get_total()
        ];
    }

    /**
     * Call thankyou page render
     *
     * @since 1.0.0
     * @return void
     */
    public function show_thankyou_page()
    {
        new CreditThankyou;
    }

    /**
     * Override: Get customer address
     *
     * @since 1.2.0
     * @param object $wc_order
     * @param string $type
     *
     * @return array
     */
    protected function get_address( $wc_order, $type = 'billing' )
    {
        $billing  = $wc_order->get_address( 'billing' );
        $shipping = $wc_order->get_address( 'shipping' );

        $address  = [];

        if ($type === 'shipping' && $this->validate_address_fields( $shipping )) {
            $address = $shipping;
        } else if ( $this->validate_address_fields( $billing ) ) {
            $address = $billing;
        }

        if ( ! empty( $address ) ) {
            $postcode = preg_replace( '/[^0-9]/', '', $address['postcode'] );

            return [
                'street'        => $address['address_1'],
                'line_2'        => $address['address_2'],
                'number'        => $address['number'],
                'district'      => $address['neighborhood'],
                'zip_code'      => $address['postcode'],
                'city'          => $address['city'],
                'state'         => $address['state'],
                'country_code'  => $address['country'],
            ];
        }


        $message = __( "Invalid address fields! Please check that the fields are filled in correctly.", "wc-koin-official" );
        $this->abort_payment_process($message);
    }

    public function setInstallmentFee($data, $gateway, $installments)
    {
        $rateValue = $this->getInstallmentRateValue($gateway, $installments);

        if ($rateValue) {
            $value = (float) $data['amount']['value'];

            if ($this->get_option('rate_transfer_format') == 1) {
                $rateValue = ($rateValue / 100) * $value;
            }

            $value += $rateValue;

            $data['amount']['value'] = number_format($value, 2, '.', '');
            $data['amount']['breakdown']['taxes'] = [
                'currency_code' => get_woocommerce_currency(),
                'value' => number_format($rateValue, 2, '.', '')
            ];
        }

        return $data;
    }

    public function setInstallmentTaxe(WC_Order $order)
    {
        $installments = $this->get_post_vars('wko-card-installments');
        $rateValue = $this->getInstallmentRateValue('credit', $installments);

        if ($rateValue) {
            $value = $order->get_total();

            if ($this->get_option('rate_transfer_format') == 1) {
                $rateValue = ($rateValue / 100) * $value;
            }

            $fee = new WC_Order_Item_Fee();
            $fee->set_name(__('Credit Card Interest', 'wc-koin-official'));
            $fee->set_amount($rateValue);
            $fee->set_total($rateValue);
            $order->add_item($fee);

            $order->calculate_totals();
            $order->save();
        }
    }

    private function getInstallmentRateValue($gateway, $installments)
    {
        $rateType = $this->get_option('rate_transfer_type');
        $rateValue = 0;

        if ($gateway === 'credit' && $rateType != 0) {

            if ($rateType == 1) {
                $rateValue = $this->get_option('rate_transfer_simple');
            }

            if ($rateType == 2) {
                $rates = json_decode($this->get_option('rate_transfer_by_installments'), true);
                $rateValue = $rates[$installments];
            }
        }

        return $rateValue;
    }

}
