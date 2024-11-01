<?php

namespace WKO\Controllers\Gateways\Blocks;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use WKO\Controllers\Gateways\Gateway;
use WKO\Helpers\Config;

abstract class AbstractBlockGateway extends AbstractPaymentMethodType
{
    protected $name;
    protected Gateway $gateway;

    public function initialize(): void
    {
        $this->settings = get_option("woocommerce_{$this->name}_settings", []);

        $gateways = WC()->payment_gateways->payment_gateways();
        $this->gateway = isset($gateways[$this->name]) ? $gateways[$this->name] : null;
    }

    /**
     * phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     * @override
     */
    public function is_active(): bool
    {
        //phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        return is_null($this->gateway) ? false : $this->gateway->is_available();
    }

    /**
     * phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     * @override
     */
    public function get_payment_method_script_handles(): array
    {
        //phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $folder = str_replace('wc-koin-', '', $this->name);
        $scriptAssetPath = Config::__dist("blocks/$folder/index.assets.php");
        $scriptAsset = file_exists($scriptAssetPath)
            ? require_once $scriptAssetPath
            : array(
                'dependencies' => array(),
                'version' => Config::__version()
            );

        wp_register_script(
            $this->name,
            Config::__dist("blocks/$folder/index.js"),
            $scriptAsset['dependencies'],
            $scriptAsset['version'],
            true
        );

        return [$this->name];
    }

    protected function getSandbox(): string
    {
        if (get_option('wc_koin_settings_environment') === 'sandbox') {
            return __(
                'Modo sandbox ativado! As transações realizadas nesse modo não são reais.',
                'wc-koin-official'
            );
        }

        return '';
    }

    /**
     * phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     * @override
     */
    public function get_payment_method_data(): array
    {
        //phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $fields = array_merge([
            'title' => $this->get_setting('title'),
            'description' => $this->get_setting('description'),
            'sandbox' => $this->getSandbox(),
            'gateway' => $this->gateway->id,
        ], $this->getGatewayCustomFields());

        return apply_filters('wc-koin-official_block_checkout_fields', $fields);
    }

    abstract protected function getGatewayCustomFields(): array;
}
