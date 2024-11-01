<?php

namespace WKO\API\Routes;

class CheckoutFields extends Route
{
    public function __construct()
    {
        $this->setNamespace();
        $this->registerRoute(
            'checkout-fields',
            [$this, 'handleRequest'],
            ['POST']
        );
    }

    public function handleRequest($data)
    {
        switch ($data->get_param('type')) {
            case 'document-data':
                $this->saveDocumentData($data->get_params());
                break;

            case 'tokenize':
                $this->saveCardData($data->get_params());
                break;

            case 'ip-address':
                $this->saveIpAddress($data->get_params());
                break;

            case 'customer-session':
                $this->saveCustomerSession($data->get_params());
                break;

            default:
                $this->sendJsonResponse(
                    "Invalid Params",
                    false,
                    422
                );
            break;
        }
    }

    private function saveIpAddress($params)
    {
        if (isset($params['fields']['ip'])) {
            $this->setCookie('ip-address', $params['fields']['ip']);
        }

        $this->sendJsonResponse('success!');
    }

    private function saveCustomerSession($params)
    {
        if (isset($params['fields']['session'])) {
            $this->setCookie('customer-session', $params['fields']['session']);
        }

        $this->sendJsonResponse('success!');
    }

    private function saveDocumentData($params)
    {
        $data = $params['fields'];
        $session = [
            'personType' => $data['personType'] ?? '',
            'document' => $data['document'] ?? ''
        ];

        $this->setCookie('document-data', serialize($session));

        $this->sendJsonResponse('success!');
    }

    private function saveCardData($params)
    {
        $data = $params['fields'];
        $cardString = base64_decode($data['hash'] ?? '');

        if ($cardString) {
            $this->setCookie('token-data', $data['hash']);
            $this->sendJsonResponse('success!');
        }

    }

    private function setCookie($name, $value)
    {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        }

        setcookie($name, $value, time() + (86400 * 30), "/", get_site_url(), true, true);
    }
}
