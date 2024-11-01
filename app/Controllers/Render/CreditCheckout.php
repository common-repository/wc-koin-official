<?php

namespace WKO\Controllers\Render;

use WKO\Controllers\Gateways\Credit;
use WKO\Controllers\Gateways\Gateway;
use WKO\Controllers\Render;

/**
 * Render the credit checkout fields
 *
 * @package Controller\Render
 * @since 1.0.0
 */
class CreditCheckout extends Render
{
    public function get_installments()
    {
        $gateway  = new Credit;
        $installments = $this->getInstallmentsFee($gateway);
        $order_total  = WC()->cart->total ?? 0;
        $max_installments = (int) $gateway->get_option('installments');

        foreach ($installments as $key => $value) {
            if ($key <= $max_installments) {
                $amount = 0;

                if ($gateway->get_option('rate_transfer_format') == 1) {
                    $amount = ($value / 100) * $order_total;
                }

                if ($gateway->get_option('rate_transfer_format') == 2) {
                    $amount = $value;
                }

                $total = $order_total + $amount;

                $arr[$key] = $this->getInstallmentText($key, $total, $amount, $gateway);
            }

        }

        return $arr;
    }

    private function getInstallmentText($installment, $total, $rate, $gateway)
    {
        $value = number_format(($total / $installment), 2, '.', '');
        $total = number_format($total, 2, '.', '');
        $format = $gateway->get_option('installments_show_mode');
        $rateText = __('Sem juros', 'wc-koin-official');

        if ($rate > 0) {
            $rateText = __('Com juros', 'wc-koin-official');
        }

        switch ($format) {
            case 'price_text':
                $text = "$installment x R$$value - $rateText";
                break;
            case 'price_total':
                $text = "$installment x R$$value (R$$total)";
                break;
            case 'price_total_text':
                $text = "$installment x R$$value (R$$total) - $rateText";
                break;
            default:
                $text = "$installment x R$" . $value;
                break;
        }

        return $text;
    }

    private function getInstallmentsFee(Gateway $gateway): array
    {
        $rateType = $gateway->get_option('rate_transfer_type');
        $rates = [
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
        ];

        if ($rateType == 1) {
            for ($i=1; $i <= 12; $i++) {
                $rates[$i] = $gateway->get_option('rate_transfer_simple');
            }
        }

        if ($rateType == 2) {
            $rates = json_decode($gateway->get_option('rate_transfer_by_installments'), true);
        }

        return $rates;
    }

    private function get_hash()
    {
        $enviroment = get_option('wc_koin_settings_environment');
        if ($enviroment === 'production') {
            return get_option('wc_koin_settings_secret_key');
        } else {
            return get_option('wc_koin_settings_secret_key_test');
        }
    }

    public function get_org_id()
    {
        $enviroment = get_option('wc_koin_settings_environment');
        if ($enviroment === 'production') {
            return get_option('wc_koin_settings_org_id');
        } else {
            return get_option('wc_koin_settings_org_id_test');
        }
    }

    public function request()
    {
        $installments = $this->get_installments();
        $hash = base64_encode($this->get_hash());

        $this->render( 'templates/checkout/credit.php', [
            'installments' => $installments,
            'hash'      => $hash,
            'mode'         => get_option('wc_koin_settings_environment'),
            'org_id'       => $this->get_org_id()
        ] );
    }
}
