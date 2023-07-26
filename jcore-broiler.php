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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/consts.php';
require_once __DIR__ . '/helpers.php';

$failed = false;

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

/**
 * Checks the prerequisites for the plugin.
 *
 * @return bool
 */
function check_prerequisites(): bool {
	$pass = ( is_readable( __DIR__ . COMPOSER_AUTOLOADER ) ||
			is_readable( ABSPATH . COMPOSER_AUTOLOADER ) ) &&
		( is_readable( __DIR__ . PREFIXED_COMPOSER_AUTOLOADER ) ||
			is_readable( ABSPATH . PREFIXED_COMPOSER_AUTOLOADER ) );

	if ( $pass ) {
		return true;
	}
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	\deactivate_plugins( \plugin_basename( __FILE__ ) );
	// Ignore this as we are not doing anything else with the get variable than checking/unsetting it.
	// phpcs:ignore
	if ( isset( $_GET['activate'] ) ) {
		// phpcs:ignore
		unset( $_GET['activate'] );
	}
	add_action( 'admin_notices', __NAMESPACE__ . '\dependencies_errors' );
	return false;
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\check_prerequisites' );

/**
 * Initializes the plugins parts.
 *
 * @return void
 */
function initialize_plugin(): void {
	// Bootstrap the plugins parts, comment out the ones you don't need.
	Database\Bootstrap::init();
	// RestAPI\Bootstrap::init();
	// Options\Bootstrap::init();
}

/**
 * The registration function for the plugin.
 *
 * @return void
 */
function register_plugin_activation_hook(): void {
	$pass = check_prerequisites();
	if ( ! $pass ) {
		return;
	}
	// Create the database tables on plugin activation.
	Database\Bootstrap::create_tables();
}


/**
 * Loads the translations.
 *
 * @return void
 */
function load_translations(): void {
	load_plugin_textdomain( BROILER_TEXT_DOMAIN, false, basename( __DIR__ ) . '/languages' );
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\register_plugin_activation_hook' );
add_action( 'admin_init', __NAMESPACE__ . '\check_prerequisites' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\load_translations' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\initialize_plugin' );
