<?php

/**
 * Plugin Name: Koin Official Payments
 * Plugin URI:  https://github.com/koinlatam
 * Version:     1.3.4
 * Description: Koin Official Payments Gateways
 * Text Domain: wc-koin-official
 * Domain Path: /languages
 * License:     GPLv3 or later
 * Author:      Koin
 * Author URI:  https://github.com/koinlatam
 *
 * @link    https://github.com/koinlatam
 * @since   1.0.0
 * @package WKO
 */

if (!defined('ABSPATH')) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';
if (version_compare(phpversion(), '7.1') < 0) {

	wp_die(
		sprintf(
			"%s <p>%s</p>",
			__("The Koin Official Payments isn't compatible to your PHP version. ", 'wc-koin-official'),
			__("The PHP version has to be a less 7.1!", 'wc-koin-official')
		),
		WKO_PLUGIN_NAME . ' -- Error',
		['back_link' => true]
	);
}

require_once __DIR__ . '/app/index.php';
