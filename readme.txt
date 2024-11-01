=== Koin Official Payments for WooCommerce ===
Contributors: apiki, aguiart0
Tags: payments, Woocommerce, store, shop, checkout, cart, koin, wirecard, loja, ecommerce
Requires at least: 5.0
Tested up to: 6.6
Stable tag: 1.3.4
Requires PHP: 7.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==
Payment plugin for WooCommerce using Koin payer services

## Integração com a Koin
Este plugin utiliza a API da Koin para processar pagamentos via boleto, Pix e cartão de crédito. Ao utilizar o plugin, os dados de pagamento são transmitidos para os seguintes serviços:
- Ambiente de Produção: https://api-payments.koin.com.br
- Ambiente de Sandbox: https://api-sandbox.koin.com.br

Este plugin utiliza o serviço de criação de fingerprint de dispositivos da Despegar para aumentar a segurança nas transações realizadas via API da Koin. Esse processo ajuda a garantir que as transações não sejam interrompidas no processo de antifraude.
A URL https://securegtm.despegar.com é utilizada para capturar o fingerprint, e embora seja diferente das URLs principais da Koin, pertence ao mesmo grupo de empresas. Portanto, os mesmos termos de uso e políticas de privacidade da Koin se aplicam a este serviço.

## Termos de Uso e Políticas de Privacidade
Por favor, consulte os seguintes links para mais informações sobre os termos de uso e a política de privacidade da Koin:
- [Termos de Uso](https://termos.koin.com.br/)
- [Política de Privacidade](https://www.koin.com.br/politica-de-privacidade/)

## Integração com o ipify
Este plugin também utiliza a API do ipify (https://api.ipify.org) para obter o endereço IP do usuário, que é utilizado para aprimorar as funcionalidades de antifraude durante as transações com cartão de crédito.


Payment methods:
- Ticket payments by installments (BNPL)
- Credit Card
- PIX

= Requisitos =
- PHP version 7.4 or higher.

== Installation ==
1. Upload this plugin to your WordPress, and activate it;
2. Configure your Koin account on WooCommerce payment settings

== Screenshots ==
- Screenshots

== Changelog ==

= 1.3.4 = 2024-10-30
* Fixed variables sanitization

= 1.3.3 = 2024-10-08
* Update readme.txt service descriptions

= 1.3.2 = 2024-10-03
* Only load scripts on checkout page

= 1.3.1 = 2024-08-20
* Fixed WordPress Review Plugins Issues

= 1.3.0 = 2024-08-11
* Fixed WordPress Review Plugins Issues

= 1.2.9 = 2024-08-11
* Handle tokenize error

= 1.2.8 = 2024-08-04
* Parse tokenize card to back-end
* Refactor block checkout

= 1.2.7 = 2024-07-35
* Add credit card tax controller

= 1.2.6 = 2024-07-35
* Add discount by payment methods

= 1.2.5 = 2024-04-24
* Fix number format bug
* Remove comma in price values
* Add new HTTP Header to inform the plugin version

= 1.2.4 = 2024-03-04
* Compatibility with WooCommerce version 8.6
* Compatibility with the new block checkout
* Compatibility with WordPress version 6.4

= 1.2.3 = 2024-01-18
* Add new filter to be used by BC - Add-on for Koin Official Payments

= 1.2.2 = 2023-12-01
* Add a new errors messages for invalid card and invalid data on checkout
* Add observers on checkout scripts to improve the compatibility with other plugins and themes

= 1.2.1 = 2023-09-11
* Add a new banner image to the billet method on checkout
* Fix the redirect looping on thankyou page

= 1.2.0 = 2023-01-23
* Add credit card payment gateway
* Add pix payment gateway
* Changes the plugin settings for a new tab on WooCommerce settings

= 1.0.3 = 2023-01-20
* Fix popup script error

= 1.0.2 = 2022-10-20
* Fix person type requirement
* Add product item description

= 1.0.1 = 2022-07-28
* Fix return URL
* Refactoring logs
* Create account fields
* Request/Response logs

= 1.0.0 = 2022-05-12
* Initial Release

== Frequently Asked Questions ==

= Dúvidas sobre a Koin? =

Envie suas dúvidas [aqui](https://www.koin.com.br/atendimento/)

