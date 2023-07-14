<?php
/**
 * Plugin Name:     JCORE B(r)oilerplate plugin
 * Plugin URI:      https://jco.fi
 * Description:     A b(r)oilerplate plugin for JCORE plugins.
 * Author:          J&Co Digital Oy
 * Author URI:      https://jco.fi
 * Text Domain:     jcore-broiler
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         JcoreBroiler
 */

namespace JcoreBroiler;

require_once __DIR__ . '/consts.php';

// Load regular composer autoloader.
if ( is_readable( __DIR__ . COMPOSER_AUTOLOADER ) ) {
	require_once __DIR__ . COMPOSER_AUTOLOADER;
} elseif ( is_readable( ABSPATH . COMPOSER_AUTOLOADER ) ) {
	require_once ABSPATH . COMPOSER_AUTOLOADER;
}

// Load prefixed composer autoloader.
if ( is_readable( __DIR__ . PREFIXED_COMPOSER_AUTOLOADER ) ) {
	require_once __DIR__ . PREFIXED_COMPOSER_AUTOLOADER;
} elseif ( is_readable( ABSPATH . PREFIXED_COMPOSER_AUTOLOADER ) ) {
	require_once ABSPATH . PREFIXED_COMPOSER_AUTOLOADER;
}

use JcoreBroiler\Database;
use JcoreBroiler\RestAPI;
use JcoreBroiler\Options;

// Bootstrap the plugins parts, comment out the ones you don't need.
Database\Bootstrap::init();
// RestAPI\Bootstrap::init();
// Options\Bootstrap::init();

/**
 * The registration function for the plugin.
 *
 * @return void
 */
function register_plugin_activation_hook(): void {
	// Create the database tables on plugin activation.
	Database\Bootstrap::create_tables();
}


/**
 * Loads the translations.
 *
 * @return void
 */
function load_translations(): void {
	load_plugin_textdomain( 'tahtipollo-backend', false, basename( __DIR__ ) . '/languages' );
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\register_plugin_activation_hook' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\load_translations' );
