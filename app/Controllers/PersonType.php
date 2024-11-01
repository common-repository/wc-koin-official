<?php

namespace WKO\Controllers;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;
use Exception;
use WKO\Helpers\Config;

class PersonType implements IntegrationInterface
{
    public function get_name()
    {
        return 'person-type';
    }

    public function initialize()
    {
        $this->registerBlockScripts();
        $this->registerWooBlockEndpoint();
    }

    public static function updateBlockOrderMeta($order, $request)
    {
        $body = json_decode($request->get_body(), true);
        $data = isset($body['extensions']['wc-koin-official']) ? $body['extensions']['wc-koin-official'] : [];
        $personType = isset($data['_billing_persontype']) ? $data['_billing_persontype'] : '1';


        switch ($personType) {
            case '2':
                self::setCnpjMeta($data, $order);
                break;
            case '1':
                self::setCpfMeta($data, $order);
                break;
            default:
            throw new Exception(__("O campo 'Tipo de Pessoa' é obrigatório!", 'wc-koin-pagameto'));
                break;
        }

        $order->update_meta_data('_billing_persontype', $data['_billing_persontype']);
        $order->save();
    }

    public static function setCpfMeta(array $data, object &$order): void
    {
        if (!isset($data['_billing_cpf']) || !$data['_billing_cpf']) {
            throw new Exception(__("O campo 'CPF' é obrigatório!", 'wc-koin-pagameto'));
        }
        $order->update_meta_data('_billing_cpf', $data['_billing_cpf']);
    }

    public static function setCnpjMeta(array $data, object &$order): void
    {
        if (!isset($data['_billing_cnpj']) || !$data['_billing_cnpj']) {
            throw new Exception(__("O campo 'CNPJ' é obrigatório!", 'wc-koin-pagameto'));
        }
        $order->update_meta_data('_billing_cnpj', $data['_billing_cnpj']);
    }

    /**
     * phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     * @override
     */
    public function get_script_handles()
    {
        //phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        return ['wc-koin-official/person-type'];
    }

    /**
     * phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     * @override
     */
    public function get_editor_script_handles()
    {
        return ['wc-koin-official/person-type'];
    }

    public function get_script_data()
    {
        return [];
    }

    public function registerBlockScripts()
    {
        $scriptUrl = Config::__dist("blocks/person-type/index.js");
        $assetPath = Config::__dist("blocks/person-type/index.asset.php");

        $scriptAsset = file_exists($assetPath)
            ? require_once $assetPath
            : [
                'dependencies' => [],
                'version' => Config::__version(),
            ];

        wp_register_script(
            'wc-koin-official/person-type',
            $scriptUrl,
            $scriptAsset['dependencies'],
            $scriptAsset['version'],
            true
        );
    }

    public function registerWooBlockEndpoint(): void
    {
        woocommerce_store_api_register_endpoint_data(
            [
                'endpoint' => CheckoutSchema::IDENTIFIER,
                'namespace' => 'wc-koin-official',
                'data_callback' => [$this, 'personFieldCallback'],
                'schema_callback' => [$this, 'personFieldSchemaCallback'],
                'schema_type' => ARRAY_A,
            ]
        );
    }

    public function personFieldCallback(): array
    {
        return [
            '_billing_persontype' => '1',
            '_billing_cpf' => '',
            '_billing_cnpj' => '',
        ];
    }

    public function personFieldSchemaCallback(): array
    {
        return [
            '_billing_persontype' => [
                'description' => __('Tipo de Pessoa', 'wc-koin-official'),
                'type' => ['int', 'null'],
                'readonly' => true,
            ],
            '_billing_cpf' => [
                'description' => __('CPF', 'wc-koin-official'),
                'type' => ['string', 'null'],
                'readonly' => true,
            ],
            '_billing_cnpj' => [
                'description' => __('CNPJ', 'wc-koin-official'),
                'type' => ['string', 'null'],
                'readonly' => true,
            ],
        ];
    }


    protected function get_file_version($file)
    {
        if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG && file_exists($file)) {
            return filemtime($file);
        }
        return ORDD_BLOCK_VERSION;
    }
}
