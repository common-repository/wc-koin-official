<?php

namespace WKO\Controllers;

use WKO\Controllers\Gateways\Billet;

class Settings
{
	public function __construct() {
        add_filter( 'plugin_row_meta', [ $this, 'koin_support_links' ], 10, 2 );
        add_filter( 'woocommerce_settings_tabs_array', [ $this, 'koin_add_settings_tab' ], 50 );
        add_action( 'woocommerce_settings_tabs_koin-settings', [ $this, 'koin_tab_content' ] );
        add_action( 'woocommerce_update_options_koin-settings', [ $this, 'koin_update_settings' ] );
    }

    /**
     * Get older options from gateway for compatibility
     *
     * @since 1.2.0
     * @return void
     */
    public function koin_settings_compatibility()
    {
        $option = get_option('koin_settings_compatibility', false);
        if (! $option || $option !== true) {
            $gateway  = new Billet;
            $testmode = $gateway->get_option('testmode');

            $data = [
                'wc_koin_settings_environment'       => $testmode === 'yes' ? 'sandbox' : 'production',
                'wc_koin_settings_code'              => $gateway->get_option('store_name'),
                'wc_koin_settings_account'           => $gateway->get_option('store_account'),
                'wc_koin_settings_secret_key'        => $gateway->get_option('secret_key'),
                'wc_koin_settings_code_test'         => $gateway->get_option('test_store_name'),
                'wc_koin_settings_account_test'      => $gateway->get_option('test_store_account'),
                'wc_koin_settings_secret_key_test'   => $gateway->get_option('test_secret_key'),
                'wc_koin_settings_status'            => $gateway->get_option('success_order_status'),
                'wc_koin_settings_logs'              => $gateway->get_option('enable_log'),
                'wc_koin_settings_my_account'        => $gateway->get_option('enable_my_account'),
                'wc_koin_settings_sync'              => $gateway->get_option('enable_sync_option')
            ];

            $this->save_koin_options($data);
        }
    }

    /**
     * Save koin compatibility options
     *
     * @since 1.2.0
     * @param array $data
     * @return void
     */
    public function save_koin_options( $data )
    {
        foreach ($data as $option => $value) {
            update_option( $option, $value );
        }

        add_option('koin_settings_compatibility', true);
    }

    /**
     * Add support link page
     *
     * @since 1.2.0
     * @param array $links
     * @param string $name
     * @return array
     */
    public function koin_support_links( $links, $name )
    {

        if ( $name === 'wc-koin-official/wc-koin-official.php' ) {
            $links[] = '<a href="https://apiki.com/">'.__( 'Suporte', 'wc-koin-official' ).'</a>';
        }

        return $links;
    }

    /**
     * Add a settings tab to the settings WooCommerce
     *
     * @since  1.2.0
     * @param array $settings_tabs
     * @return array
     */
    public function koin_add_settings_tab( $tabs )
    {
        $tabs['koin-settings'] = __( 'Koin', 'wc-koin-official' );

        return $tabs;
    }

    /**
     * Output the tab content
     *
     * @since  1.2.0
     * @return void
     */
    public function koin_tab_content()
    {
        woocommerce_admin_fields( $this->koin_get_fields() );
    }

    /**
     * Get the setting fields
     *
     * @since  1.2.0
     * @return array $fields
     */
    public function koin_get_fields()
    {
        $fields = [
            'section_title' => [
                'name' => __( 'Koin Settings', 'wc-koin-official' ),
                'type' => 'title',
                'desc' => '',
                'id'   => 'wc_koin_settings_title'
            ],
            'environment' => [
                'name'    => __( 'Environment', 'wc-koin-official' ),
                'type'    => 'select',
                'desc'    => __( 'Select the environment, homologation or production.', 'wc-koin-official' ),
                'id'      => 'wc_koin_settings_environment',
                'options' => [
                    'sandbox'    => __( 'Sandbox', 'wc-koin-official' ),
                    'production' => __( 'Production', 'wc-koin-official' ),
                ],
            ],
            'store_code' => [
                'name'    => __( 'Store Code', 'wc-koin-official' ),
                'type'    => 'text',
                'desc'    => __( 'Enter your store code.', 'wc-koin-official' ),
                'id'      => 'wc_koin_settings_code',
            ],
            'store_account' => [
                'name'    => __( 'Store Account', 'wc-koin-official' ),
                'type'    => 'text',
                'desc'    => __( 'Enter your store account.', 'wc-koin-official' ),
                'id'      => 'wc_koin_settings_account',
            ],
            'secret_key' => [
                'name'    => __( 'Secret Key', 'wc-koin-official' ),
                'type'    => 'password',
                'desc'    => __( 'Enter your secret key.', 'wc-koin-official' ),
                'id'      => 'wc_koin_settings_secret_key',
            ],
            'org_id' => [
                'name'    => __( 'Organization ID', 'wc-koin-official' ),
                'type'    => 'text',
                'desc'    => __( 'Enter your organization.', 'wc-koin-official' ),
                'id'      => 'wc_koin_settings_org_id',
            ],
            'store_code_test' => [
                'name'    => __( 'Test Store Code', 'wc-koin-official' ),
                'type'    => 'text',
                'desc'    => __( 'Enter your test store code.', 'wc-koin-official' ),
                'id'      => 'wc_koin_settings_code_test',
            ],
            'store_account_test' => [
                'name'    => __( 'Test Store Account', 'wc-koin-official' ),
                'type'    => 'text',
                'desc'    => __( 'Enter your test store account.', 'wc-koin-official' ),
                'id'      => 'wc_koin_settings_account_test',
            ],
            'secret_key_test' => [
                'name'    => __( 'Test Secret Key', 'wc-koin-official' ),
                'type'    => 'password',
                'desc'    => __( 'Enter your test secret key.', 'wc-koin-official' ),
                'id'      => 'wc_koin_settings_secret_key_test',
            ],
            'org_id_test' => [
                'name'    => __( 'Test Organization ID', 'wc-koin-official' ),
                'type'    => 'text',
                'desc'    => __( 'Enter your organization.', 'wc-koin-official' ),
                'id'      => 'wc_koin_settings_org_id_test',
            ],
            "success_order_status" => [
                'title'       => __( 'Success status for orders', 'wc-koin-official' ),
                'type'        => 'select',
                'id'          => 'wc_koin_settings_status',
                'options'     => [
                    'wc-processing' => __( 'Processing', 'wc-koin-official' ),
                    'wc-completed'  => __( 'Completed', 'wc-koin-official' )
                ],
                'description' => __( 'This field defines what status the order must have after being successfully completed.', 'wc-koin-official' ),
            ],
            "log" => [
                "title"       => "Logs",
                "desc"       => __( "Enable Woocommerce Logs for Koin.", 'wc-koin-official' ),
                "type"        => "checkbox",
                'id'          => 'wc_koin_settings_logs',
                "description" => sprintf(
                    "%s<a href='admin.php?page=wc-status&tab=logs'>Woocommerce->Status->Logs</a>",
                    __( "To View the logs click the link: ", 'wc-koin-official' ),
                ),
                "default"     => "yes"
            ],
            "my_account" => [
                "title"       => __( "My Account", 'wc-koin-official' ),
                "desc"       => __( "Enable Koin menu on my account page.", 'wc-koin-official' ),
                "type"        => "checkbox",
                'id'          => 'wc_koin_settings_my_account',
                "description" => __( "Create a menus on Woocommerce my account page.", 'wc-koin-official' ),
                "default"     => "yes"
            ],
            "enable_sync_option" => [
                "title"       => __( "Sync orders option", 'wc-koin-official' ),
                "desc"       => __( "Enable manual order synchronization option.", 'wc-koin-official' ),
                "type"        => "checkbox",
                'id'          => 'wc_koin_settings_sync',
                "description" => sprintf(
                    "<span class='warning'><i class='fas fa-exclamation-triangle'></i><strong>%s</strong></span>%s",
                    __( " Waring! ", 'wc-koin-official' ),
                    __( "Activating this option you will have access to a manual status synchronization option within the order pages. Only enable this option if you know what you are doing." , 'wc-koin-official' )
                ),
                "default"     => "no"
            ],
            'section_end' => [
                'type' => 'sectionend',
                'id'   => 'wc_koin_settings_section_end'
            ]
        ];

        return apply_filters( 'wc_koin_tab_settings', $fields );
    }

    /**
     * Update the settings
     *
     * @since  1.2.0
     * @return void
     */
    public function koin_update_settings()
    {
        woocommerce_update_options( $this->koin_get_fields() );
    }

}
