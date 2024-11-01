<?php

namespace WKO;

// Define names
define( 'WKO_PLUGIN_NAME', 'Koin Official Payments' );
define( 'WKO_PLUGIN_SLUG', 'wc-koin-official' );
define( 'WKO_PLUGIN_NAMESPACE', 'WKO' );
define( 'WKO_PLUGIN_PREFIX', 'wko' );

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'add_action' ) ) exit;

require_once 'Helpers/Hooks.php';
