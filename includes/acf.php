<?php

namespace JcoreBroiler;

use RuntimeException;

/**
 * Adds the ACF JSON load directory, to allow for ACF fields to be versioned, and loaded faster.
 *
 * @param array $paths The paths to load the JSON from.
 * @return array
 *
 * @throws RuntimeException When the directory cannot be created.
 */
function acf_json_load_point( array $paths ): array {
	$acf_json_path = BROILER_PLUGIN_PATH . '/acf-json';
	if ( ! @mkdir( $acf_json_path ) && ! is_dir( $acf_json_path ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions, WordPress.PHP.NoSilencedErrors.Discouraged
		// Add an admin notice.
		add_action(
			'admin_notices',
			static function() {
				?>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'Could not create the ACF JSON directory. Please create it manually.', 'jcore-broiler' ); ?></p>
			</div>
				<?php
			}
		);
	}

	$paths[] = $acf_json_path;
	return $paths;
}
add_filter( 'acf/settings/load_json', __NAMESPACE__ . '\acf_json_load_point' );

/**
 * Adds the ACF JSON save directory, to allow for ACF fields to be versioned, and loaded faster.
 *
 * This is called pretty early on, to allow for overwrites later.
 *
 * @param string $path The path to save the JSON to.
 * @return string
 *
 * @throws RuntimeException When the directory cannot be created.
 */
function acf_json_save_point( string $path ): string {
	if ( ! empty( $path ) ) {
		return $path;
	}

	$acf_json_path = BROILER_PLUGIN_PATH . '/acf-json';
	if ( ! @mkdir( $acf_json_path ) && ! is_dir( $acf_json_path ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions, WordPress.PHP.NoSilencedErrors.Discouraged
		// Add an admin notice.
		add_action(
			'admin_notices',
			static function() {
				?>
				<div class="notice notice-error">
					<p><?php esc_html_e( 'Could not create the ACF JSON directory. Please create it manually.', 'jcore-broiler' ); ?></p>
				</div>
				<?php
			}
		);
	}

	return $acf_json_path;
}
add_filter( 'acf/settings/save_json', __NAMESPACE__ . '\acf_json_save_point', 5 );
