<?php

namespace WKO\Controllers\Gateways;

use DateInterval;
use DateTime;
use WKO\Controllers\Render\PixThankyou;
use WKO\Helpers\Config;

/**
 * Name: Credit
 * Structure the billet payment method
 * @package Controllers\Gateways
 * @since 1.0.0
 */
class Pix extends Gateway implements InterfaceGateways
{

    public function __construct() {

        $this->id                 = "wc-koin-pix";
        $this->payment_method     = "pix";
        $this->has_fields         = false;
        $this->method_title       = __( "Koin - Pay with PIX", 'wc-koin-official' );
        $this->method_description = sprintf( "<div class='description'>%s <a target='_blank' href='%s'>%s<i class='fa-solid fa-up-right-from-square'></i></a></div>",
                                        __( "Pay in using PIX payment method!", 'wc-koin-official' ),
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
     *
     * @since 1.0.0
     * @return void
     */
    public function init_form_fields()
    {

        $this->form_fields = [
            "enabled" => [
                "title"       => __( "Enable", 'wc-koin-official' ),
                "label"       => __( "Enable payment with PIX", 'wc-koin-official' ),
                "type"        => "checkbox",
                "description" => __( "Check this option to activate the Koin payment method", 'wc-koin-official' ),
                "default"     => "no",
                "desc_tip"    => true
            ],

            "title" => [
                "title"       => __( "Title", 'wc-koin-official' ),
                "type"        => "text",
                "description" => __( "This controls the title which the user sees during checkout.", 'wc-koin-official' ),
                "default"     => __( "Pix", 'wc-koin-official' ),
                "desc_tip"    => true
            ],

            "description" => [
                "title"       => __( "Description", 'wc-koin-official' ),
                "type"        => "textarea",
                "description" => __( "This controls the description which the user sees during checkout.", 'wc-koin-official' ),
                "default"     => __( "Pay using PIX payment method!", 'wc-koin-official' ),
                "desc_tip"    => true
            ],

            "expiration" => [
                "title"       => __( "Expiration time", 'wc-koin-official' ),
                "type"        => "select",
                "options"     => [
                    '1'  => __('1 Hour', 'wc-koin-official'),
                    '2'  => __('2 Hours', 'wc-koin-official'),
                    '5'  => __('5 Hours', 'wc-koin-official'),
                    '10' => __('10 Hours', 'wc-koin-official'),
                    '24' => __('24 Hours', 'wc-koin-official'),
                ],
                "description" => __( "This controls the expiration time for pix QR Code.", 'wc-koin-official' ),
                "default"     => 1,
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
     *
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
    }

    /**
     * Validate the payment fields
     *
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
        $hour = $this->get_option('expiration');

        if ($hour) {
            $expiration_date = new DateTime();
            $expiration_date->add(new DateInterval("PT{$hour}H"));

            return [
                "code" => "PIX",
                "expiration_date" => $expiration_date->format("Y-m-d\TH:i:s") . ".000Z"
            ];
        }

        return [];
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
        return [];
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
        if (isset($body->order_id)) {
            $metas['_order_id'] = $body->order_id;
        }

        if (isset($body->locations)) {
            $location = (array) end($body->locations);

            if (isset($location['location'])) {
                $location = (array) $location['location'];

                $metas['_line']    = $location['emv'];
                $metas['_qr_code'] = $location['qr_code'];

                $wc_order->add_order_note( sprintf( "<strong>%s</strong> : %s %s",
                    WKO_PLUGIN_NAME,
                    __( 'Koin pix line:', 'wc-koin-official' ),
                    $location['emv']
                ), true );
            }
        }

        return $metas;
    }

    /**
     * Call thankyou page render
     *
     * @since 1.2.0
     * @return void
     */
    public function show_thankyou_page()
    {
        new PixThankyou;
    }
}
