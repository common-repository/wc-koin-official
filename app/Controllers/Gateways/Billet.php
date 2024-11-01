<?php

namespace WKO\Controllers\Gateways;

use DateInterval;
use DateTime;
use WKO\Controllers\Render\BilletCheckout;
use WKO\Controllers\Render\BilletThankyou;
use WKO\Helpers\Config;

/**
 * Name: Billet
 * Structure the billet payment method
 * @package Controllers\Gateways
 * @since 1.0.0
 */
class Billet extends Gateway implements InterfaceGateways
{

    public function __construct() {

        $this->id                 = "wc-koin-billet";
        $this->payment_method     = "billet";
        $this->icon               = esc_url( Config::__image( 'koin/b-koin.png' ) );
        $this->has_fields         = false;
        $this->method_title       = __( "Koin - Payment in installments", 'wc-koin-official' );
        $this->method_description = sprintf( "<div class='description'>%s <a target='_blank' href='%s'>%s<i class='fa-solid fa-up-right-from-square'></i></a></div>",
                                        __( "Pay in installments by ticket slip at Koin!", 'wc-koin-official' ),
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

        parent::__construct();
    }

    /**
     * Create/Edit billet gateway options
     * @since 1.0.0
     * @return void
     */
    public function init_form_fields()
    {

        $this->form_fields = [
            "enabled" => [
                "title"       => __( "Enable", 'wc-koin-official' ),
                "label"       => __( "Enable payment in installments by ticket", 'wc-koin-official' ),
                "type"        => "checkbox",
                "description" => __( "Check this option to activate the Koin payment method", 'wc-koin-official' ),
                "default"     => "no",
                "desc_tip"    => true
            ],

            "title" => [
                "title"       => __( "Title", 'wc-koin-official' ),
                "type"        => "text",
                "description" => __( "This controls the title which the user sees during checkout.", 'wc-koin-official' ),
                "default"     => __( "Koin - Ticket Slip", 'wc-koin-official' ),
                "desc_tip"    => true
            ],

            "description" => [
                "title"       => __( "Description", 'wc-koin-official' ),
                "type"        => "textarea",
                "description" => __( "This controls the description which the user sees during checkout.", 'wc-koin-official' ),
                "default"     => __( "Ticket payments by installments", 'wc-koin-official' ),
                "desc_tip"    => true
            ],


            "discount_type" => [
				'title'       => __('Discount Type', 'wc-koin-official'),
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
				'title'       => __('Discount Value', 'wc-koin-official' ),
				'type'        => 'text',
                'class'       => 'wko-discount-value',
				'description' => __('In this option you can define the discount amount.', 'wc-koin-official' ),
				'desc_tip'    => true,
				'default'     => '0',
				'placeholder' => '0',
            ],

            "koin_logo" => [
                "type"        => "hidden",
                "default"     => esc_url( Config::__image( "koin/b-koin.png" ) ),
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

        new BilletCheckout;
    }

    /**
     * Validate the payment fields
     * @since 1.0.0
     * @return boolean
     */
    public function validate_fields()
    {
        return true;
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
        $expiration_date = new DateTime();
        $expiration_date->add( new DateInterval('P30D') );

        return [
            "code" => "BNPL",
            "expiration_date" => $expiration_date->format("Y-m-d\TH:i:s") . ".000Z"
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
        return [
            "payment_url"        => $this->get_return_url( $wc_order ),
            "verified_id"        => false,
            "ip_address"         => $this->get_customer_ip(),
            "device_fingerprint" => md5( serialize( array_map( 'sanitize_text_field', $_SERVER ) ) ),
            "shipping"           => $this->get_shipping_data( $wc_order ),
        ];
    }

    /**
     * Get gateways metas
     * @since 1.2.0
     * @param object $body
     * @param object $wc_order
     */
    protected function get_metas($body, $wc_order)
    {
        $payment_link = isset( $body->return_url ) ? $body->return_url : '';

        $wc_order->add_order_note( sprintf( "<strong>%s</strong> : %s %s",
        WKO_PLUGIN_NAME,
            __( 'Koin payment link:', 'wc-koin-official' ),
            $payment_link
        ), true );

        return [
            '_payment_link' => $payment_link
        ];
    }

    /**
     * Call thankyou page render
     * @since 1.0.0
     * @return object
     */
    public function show_thankyou_page()
    {
        new BilletThankyou;
    }
}
