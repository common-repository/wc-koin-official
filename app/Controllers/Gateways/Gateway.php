<?php

namespace WKO\Controllers\Gateways;

use DateInterval;
use DateTime;
use Exception;
use WC_Payment_Gateway;
use WKO\Controllers\Koin;
use WKO\Controllers\Logs;
use WKO\Model\Options;
use WKO\Model\PostMeta;

/**
 * Structure the billet payment method
 * @package Services
 * @since 1.0.0
 */
abstract class Gateway extends WC_Payment_Gateway
{
    protected $logger;
    protected $payment_method = '';

    public function __construct()
    {
        $this->logger = new Logs;
        add_action('woocommerce_cart_calculate_fees', [$this, 'gateway_discount']);
    }

    /**
     * Process WooCommerce order
     *
     * @since 1.2.0
     * @param int $wc_order_id
     * @return bool|array
     */
    public function process_payment($wc_order_id)
    {
        $wc_order     = wc_get_order($wc_order_id);
        $request_body = $this->build_request_body($wc_order);
        $koin         = new Koin;
        $response     = $koin->create_order($request_body);
        $body         = isset($response['body']) ? json_decode($response['body']) : [];

        if (isset($body->status) && $body->status != 'error') {

            $koin_status = $body->status;
            $status      = $this->get_order_status($koin_status->type);

            if (in_array($koin_status->type, ['Published', 'Opened', 'Collected', 'Authorized'])) {

                do_action('wc_koin_official-after_success_order', $wc_order);

                $wc_order->update_status(
                    $status,
                    sprintf(
                        "<strong>%s</strong> :",
                        WKO_PLUGIN_NAME
                    ),
                    true
                );

                $this->finishSuccessOrder($wc_order, $body);

                $this->logger->create_order("==== KOIN CREATE ORDER SUCCESS ====", $response);

                if ($koin_status->type === 'Authorized') {
                    $this->capture_order($koin, $body, $wc_order);
                }

                return [
                    'result' => 'success',
                    'redirect' => $this->get_return_url($wc_order)
                ];
            } else {
                $this->order_failed($wc_order);
                $this->logger->create_order(
                    "==== KOIN CREATE ORDER ERROR ====",
                    $response
                );

                return $this->handle_failed_status(isset($koin_status->reason) ? $koin_status->reason : '');
            }
        } else {
            $this->order_failed($wc_order);
            $this->logger->create_order(
                "==== KOIN CREATE ORDER ERROR ====",
                $response
            );
        }

        $this->abort_payment_process(
            __('An unknown error has occurred. Please, contact Us.', 'wc-koin-official')
        );
    }

    private function finishSuccessOrder($order, $body)
    {
        global $woocommerce;

        wc_reduce_stock_levels($order->get_id());

        $order->add_order_note(sprintf(
            "<strong>%s</strong> : %s",
            WKO_PLUGIN_NAME,
            __('Waiting for credit confirmation from Koin.', 'wc-koin-official')
        ), true);


        if (get_option('wc_koin_settings_environment') === 'sandbox') {
            $order->add_order_note(sprintf(
                "<strong>%s</strong> : %s",
                WKO_PLUGIN_NAME,
                __('Test mode activate! In this mode transactions are not real.', 'wc-koin-official')
            ), true);
        }

        $metas = $this->get_metas($body, $order);
        $this->save_post_meta($order->get_id(), $metas);
        $woocommerce->cart->empty_cart();
    }

    private function handle_failed_status($reason): bool
    {
        $message = '';

        switch ($reason) {
            case 'InvalidData':
                $message = __('Payment rejected! The transaction was declined due to a processing error. Please, contact us.', 'wc-koin-official');
                break;
            case 'InvalidCard':
                $message = __('Payment rejected! Invalid credit card details. Please, try again.', 'wc-koin-official');
                break;
            default:
                $message = __('Payment rejected! It was not possible to make the payment.', 'wc-koin-official');
                break;
        }

        $this->abort_payment_process($message);
    }

    private function capture_order($koin, $body, $wc_order)
    {
        $response = $koin->capture_order($body->order_id);
        $body     = isset($response['body']) ? json_decode($response['body']) : false;

        if (!isset($response['error']) && isset($body->status)) {

            if (isset($body->status->type)) {
                $status = $this->get_order_status($body->status->type);

                $wc_order->update_status(
                    $status,
                    sprintf(
                        "<strong>%s</strong> :",
                        WKO_PLUGIN_NAME,
                    ),
                    true
                );

                $this->logger->create_order(
                    sprintf(
                        "==== KOIN ORDER CAPTURED SUCCESS ====\n- %s",
                        __('Order captured:', 'wc-koin-official')
                    ),
                    $response
                );

                return;
            }
        }

        $this->logger->create_order(
            sprintf(
                "==== KOIN ORDER CAPTURED ERROR ====\n- %s",
                $response['message']
            ),
            $response['response']
        );

        return;
    }

    /**
     * @since 1.2.0
     * @param object $wc_order
     */
    protected function order_failed($wc_order)
    {
        $wc_order->update_status(
            'wc-failed',
            sprintf(
                "<strong>%s</strong> : %s",
                WKO_PLUGIN_NAME,
                __('It was not possible to make the payment. Payment rejected!', 'wc-koin-official')
            ),
            true
        );
    }

    /**
     * @since 1.2.0
     * @param string $koin_status
     */
    private function get_order_status($koin_status)
    {
        $success = get_option('wc_koin_settings_status');

        switch ($koin_status) {
            case 'Collected':
                $status = $success;
                break;

            case 'Published':
                $status = 'wc-on-hold';
                break;

            case 'Opened':
                $status = 'wc-awaiting-analysis';
                break;

            case 'Failed':
                $status = 'wc-failed';
                break;

            case 'Cancelled':
                $status = 'wc-cancelled';
                break;

            case 'Authorized':
                $status = 'wc-awaiting-analysis';
                break;

            default:
                $status = 'wc-awaiting-analysis';
                break;
        }

        return $status;
    }

    /**
     * Build request body
     *
     * @since 1.2.0
     * @param object $wc_order
     */
    private function build_request_body($wc_order)
    {

        $billing_address  = $this->get_address($wc_order, 'billing');
        $store            = $this->get_store_data();

        $body = [
            "store" => [
                'category' => "",
                'code'     => $store['code']
            ],
            "transaction"      => $this->get_transaction_data($wc_order, $store),
            "payment_method"   => $this->get_payment_method($wc_order),
            "payer"            => $this->get_payer_data($wc_order, $billing_address),
            "items"            => $this->get_order_items($wc_order),
            "notification_url" => [
                $this->get_webhook_url()
            ],

            "country_code"       => $billing_address['country_code'],
        ];

        $body = array_merge($body, $this->get_custom_gateway_fields($wc_order));

        return $body;
    }

    /**
     * Get order shipping data
     *
     * @since 1.2.0
     * @param object $wc_order
     * @return array
     */
    protected function get_shipping_data($wc_order)
    {
        $delivery_date = new DateTime();
        $delivery_date->add(new DateInterval('P30D'));

        return [
            "address"       => $this->get_address($wc_order, 'shipping'),
            "delivery_date" => $delivery_date->format('Y-m-d')
        ];
    }

    /**
     * Get user IP number
     *
     * @since 1.2.0
     * @return string
     */
    protected function get_customer_ip()
    {
        if ($this->get_server_vars('HTTP_CLIENT_IP')) {
            return $this->get_server_vars('HTTP_CLIENT_IP');
        }

        if ($this->get_server_vars('HTTP_X_FORWARDED_FOR')) {
            return $this->get_server_vars('HTTP_X_FORWARDED_FOR');
        }

        return $this->get_server_vars('REMOTE_ADDR');
    }

    /**
     * Get customer data
     *
     * @since 1.2.0
     * @param object $wc_order,
     * @param array $address
     *
     * @return array
     */
    protected function get_payer_data($wc_order, $address)
    {
        $customer = $this->get_customer($wc_order);
        $phones   = $this->get_phones($wc_order);

        return [
            "phone"      => isset($phones['home_phone']) ? $phones['home_phone'] : '',
            "first_name" => $customer['first_name'],
            "last_name"  => $customer['last_name'],
            "full_name"  => $customer['full_name'],
            "email"      => $customer['email'],
            "address"    => $address,
            "document"   => [
                "nationality" => $address['country_code'],
                "type"        => $customer['type'],
                "number"      => $customer['document']
            ]
        ];
    }

    /**
     * Get store data
     *
     * @since 1.2.0
     * @return array
     */
    private function get_store_data()
    {
        $code    = get_option("wc_koin_settings_code");
        $account = get_option("wc_koin_settings_account");

        if (get_option("wc_koin_settings_environment") === 'sandbox') {
            $code    = get_option("wc_koin_settings_code_test");
            $account = get_option("wc_koin_settings_account_test");
        }

        return [
            'code'    => $code,
            'account' => $account
        ];
    }

    /**
     * Get the transaction data
     *
     * @since 1.2.0
     * @param object $wc_order
     * @param array $store_data
     *
     * @return array
     */
    protected function get_transaction_data($wc_order, $store_data)
    {
        $shipping_total = (float) $wc_order->get_shipping_total();
        $tax_total = (float) $wc_order->get_total_tax();
        $order_total = (float) $wc_order->get_total();
        $items_total = ($order_total - $tax_total) - $shipping_total;

        $data = [
            "amount" => [
                "value"     => $this->number_format($order_total),
                "breakdown" => [
                    "items" => [
                        "currency_code" => $wc_order->get_currency(),
                        "value"         => $this->number_format($items_total)
                    ],
                    "shipping" => [
                        "currency_code" => $wc_order->get_currency(),
                        "value"         => $this->number_format($shipping_total)
                    ],
                    "taxes" => [
                        "currency_code" => $wc_order->get_currency(),
                        "value"         => $this->number_format($tax_total)
                    ]
                ],
                "currency_code" => $wc_order->get_currency()
            ],
            "account"      => $store_data['account'],
            "reference_id" => bin2hex(random_bytes(3)) . "_" . $wc_order->get_id()
        ];

        $installments = $this->payment_method === 'credit' ? $this->get_post_vars('wko-card-installments') : 1;

        return apply_filters(
            'wc_koin_transaction_data',
            $data,
            $this->payment_method,
            $installments
        );
    }

    /**
     * Format Numbers
     *
     * @since 1.2.5
     * @param float $value
     */
    private function number_format($value)
    {
        return number_format($value, 2, '.', '');
    }

    /**
     * Get order items
     *
     * @since 1.2.0
     * @param object $wc_order
     *
     * @return array
     */
    protected function get_order_items($wc_order)
    {
        $items = [];
        $cart  = $wc_order->get_items();
        $discounts = $this->get_discounts($wc_order);
        $discount  = $discounts / count($wc_order->get_items());

        foreach ($cart as $key => $item) {
            $product = $item->get_product();
            if ($product) {
                $product_id  = $product->get_parent_id() == 0 ? $product->get_id() : $product->get_parent_id();
                $amount      = floatval($product->get_price()) - $discount;
                $quantity    = $item->get_quantity();
                $code        = "WC-{$product_id}";

                $cats = wc_get_product_term_ids($product_id, 'product_cat');
                $cat  = empty($cats) ? ['name' => 'Uncategorized', 'term_id' => 0] : (array) get_term_by('id', $cats[0], 'product_cat');

                array_push($items, [
                    "category"    => [
                        "id"   => (string) $cat['term_id'],
                        "name" => $cat['name']
                    ],
                    "id"          => $code,
                    "name"        => $item->get_name(),
                    "price"       => [
                        'currency_code' => $wc_order->get_currency(),
                        'value'         => $this->number_format($amount)
                    ],
                    "quantity"    => $quantity,
                    "type"        => 'Generic',
                    "discount"    => [
                        "currency" => $wc_order->get_currency(),
                        "value"    => $this->number_format($discount)
                    ],
                ]);
            }
        }

        return $items;
    }

    /**
     * Get customer address
     *
     * @since 1.2.0
     * @param object $wc_order
     * @param string $type
     *
     * @return array
     */
    protected function get_address($wc_order, $type = 'billing')
    {
        $billing  = $wc_order->get_address('billing');
        $shipping = $wc_order->get_address('shipping');

        $address  = [];

        if ($type === 'shipping' && $this->validate_address_fields($shipping)) {
            $address = $shipping;
        } else if ($this->validate_address_fields($billing)) {
            $address = $billing;
        }

        if (!empty($address)) {

            if (!isset($address['neighborhood']) || !$address['neighborhood']) {
                $address['neighborhood'] = $address['address_1'];
            }

            if (!isset($address['number']) || !$address['number']) {
                $address['number'] = $address['postcode'];
            }

            return [
                'street'        => $address['address_1'],
                'line_2'        => $address['address_2'],
                'number'        => $address['number'],
                'district'      => $address['neighborhood'],
                'zip_code'      => $address['postcode'],
                'city_name'     => $address['city'],
                'state'         => $address['state'],
                'country_code'  => $address['country'],
            ];
        }

        $message = __("Invalid address fields! Please check that the fields are filled in correctly.", "wc-koin-official");
        $this->abort_payment_process($message);
    }

    /**
     * Validade address fields
     *
     * @since 1.2.0
     * @param array $address
     *
     * @return bool
     */
    protected function validate_address_fields($address)
    {
        $needed = ['address_1', 'city', 'state', 'postcode', 'country'];
        $validate = true;

        foreach ($address as $key => $field) {
            if (in_array($key, $needed)) {
                if (!$field) {
                    $validate = false;
                }
            }
        }

        return $validate;
    }

    /**
     * Get customer checkout data
     *
     * @since 1.2.0
     * @param object $wc_order
     *
     * @return array
     */
    private function get_customer($wc_order)
    {
        $billing_first_name = $wc_order->get_billing_first_name();
        $billing_last_name  = $wc_order->get_billing_last_name();

        $person_type = $this->get_person($wc_order);


        $name     = "$billing_first_name $billing_last_name";
        $mail     = $wc_order->get_billing_email();
        $type     = $person_type['person'];
        $document = $person_type['document'];

        return [
            'first_name' => $billing_first_name,
            'last_name'  => $billing_last_name,
            'full_name'  => $name,
            'email'      => $mail,
            'type'       => $type,
            'document'   => $document
        ];
    }

    /**
     * Get customer person type and document
     *
     * @since 1.2.0
     * @return array
     */
    private function get_person($order)
    {
        $personType = $order->get_meta('_billing_persontype');
        $cnpj = $order->get_meta('_billing_cnpj');
        $cpf = $order->get_meta('_billing_cpf');

        $person = '';
        $document = '';

        switch ($personType) {
            case '2':
                $person = 'cnpj';
                $document = $cnpj;
                break;
            case '1':
                $person = 'cpf';
                $document = $cpf;
                break;
            default:
                $person = $cpf ? 'cpf' : 'cnpj';
                $document = $cpf ?? $cnpj;
                break;
        }

        return [
            'person'   => $person,
            'document' => preg_replace('/[^0-9]/', '', $document)
        ];
    }

    /**
     * Get customer phones
     *
     * @since 1.2.0
     * @param object $wc_order
     * @param array $phones
     *
     * @return array
     */
    private function get_phones($wc_order, $phones = [])
    {
        $billing_phone = $wc_order->get_billing_phone();
        if ($billing_phone) {
            $number      = $billing_phone ? str_replace('/[^\d]+/', '', $billing_phone) : "";
            $area       = $number;

            $area        = $area ? preg_replace('/\A.{2}?\K[\d]+/', '', $area) : "";
            $number      = $number ? preg_replace('/^\d{2}/', '', $number)  : "";

            $phones['home_phone'] = [
                'country_code' => '55',
                'area'         => $area,
                'number'       => $number,
                'type'         => 'phone'
            ];
        }


        $billing_cell  = $this->get_post_vars('billing_cellphone');
        if ($billing_cell) {
            $number      = $billing_phone ? str_replace('/[^\d]+/', '', $billing_cell) : "";
            $area       = $number;

            $area        = $area ? preg_replace('/\A.{2}?\K[\d]+/', '', $area) : "";
            $number      = $number ? preg_replace('/^\d{2}/', '', $number)  : "";

            $phones['mobile_phone'] = [
                'country_code' => '55',
                'area'         => $area,
                'number'       => $number,
                'type'         => 'phone'
            ];
        }

        return $phones;
    }

    /**
     * Get order price discounts
     *
     * @since 1.2.0
     * @param object $wc_order
     *
     * @return float
     */
    protected function get_discounts($wc_order)
    {
        $count = count($wc_order->get_items());
        $discount = 0;

        foreach ($wc_order->get_items('fee') as $item_id => $item_fee) {
            $total = floatval($item_fee->get_total());

            if ($total < 0) {
                $discount += $total * -1;
            }
        }

        $discount += floatval($wc_order->get_total_discount());
        return $discount / $count;
    }

    /**
     * Get Webhook URL
     * @since 1.0.0
     * @return string
     */
    private function get_webhook_url()
    {
        $opt   = new Options;
        $token = $opt->get('_webhook_token_orders');
        $url   = get_site_url();

        return "$url/wc-api/koin_orders_$token";
    }

    /**
     * Get $_POST global var itens
     *
     * @since 1.2.0
     * @param string $var
     *
     * @return mixed
     */
    protected function get_post_vars($name)
    {
        if (isset($_POST[$name]) && !empty($_POST[$name])) {
            return sanitize_text_field(wp_unslash($_POST[$name]));
        }

        return false;
    }

    /**
     * Get $_SERVER global var itens
     *
     * @since 1.2.0
     * @param string $var
     *
     * @return mixed
     */
    protected function get_server_vars($name)
    {
        if (isset($_SERVER[$name]) && !empty($_SERVER[$name])) {
            return sanitize_text_field(wp_unslash($_SERVER[$name]));
        }

        return false;
    }

    /**
     * Save order meta values
     *
     * @since 1.0.0
     * @param int $order
     * @param array $metas
     *
     * @return void
     */
    protected function save_post_meta($order, $metas)
    {
        $postmeta = new PostMeta;

        foreach ($metas as $key => $meta) {
            $postmeta->create($order, $key, $meta);
        }
    }

    /**
     * Abort the payment process
     *
     * @since 1.2.0
     * @param string $message
     * @param string $type
     *
     * @return bool
     */
    protected function abort_payment_process($message, $type = "error")
    {
        throw new Exception("Koin: $message");
    }

    public function gateway_discount()
    {
        $cart = WC()->cart;
        $total = $cart->get_cart_contents_total();
        $gateway = WC()->session->get('chosen_payment_method');

        if ($gateway === $this->id) {
            $discount = (float) $this->get_option('discount_value');
            $discount_type = (int) $this->get_option('discount_type');

            if ($discount <= 0 || $discount_type === 0) {
                return;
            }

            if ($discount_type === 1) {
                $discount = ($total / 100) * $discount;
            }

            $cart->add_fee(
                __('Payment method discount', 'wc-koin-official'),
                -$discount,
                false
            );
        }
    }

    abstract protected function get_payment_method($wc_order);

    abstract protected function get_custom_gateway_fields($wc_order);

    abstract protected function show_thankyou_page();

    abstract protected function get_metas($body, $wc_order);
}
