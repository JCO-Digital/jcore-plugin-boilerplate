<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing

namespace JcoreBroiler\RestAPI;

use JcoreBroiler\RestAPI\APIs\ClassAPI;
use JcoreBroiler\RestAPI\APIs\SchoolAPI;
use JcoreBroiler\RestAPI\APIs\ExampleAPI;

/**
 * Bootstrap class, initializes the REST API.
 *
 * @package JcoreBroiler\RestAPI
 */
class Bootstrap {

	/**
	 * Array of all API classes that should be initialized.
	 *
	 * @var RestInterface[]
	 */
	public static array $apis = array();


	/**
	 * Initializes the REST APIs.
	 *
	 * @return void
	 */
	public static function init(): void {
		foreach ( self::$apis as $api ) {
			$api::init();
		}
		add_action( 'wp_enqueue_scripts', array( static::class, 'localize_scripts' ) );
		add_filter( 'allowed_http_origins', array( static::class, 'add_allowed_origins' ) );
		remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
		add_filter( 'rest_api_init', array( static::class, 'send_allowed_origin' ) );
	}

	/**
	 * Localizes the scripts.
	 *
	 * @return void
	 */
	public static function localize_scripts(): void {
		$api_urls = array();
		foreach ( self::$apis as $api ) {
			$api_urls[ $api::nice_name() ] = rest_url( $api::$namespace );
		}
		wp_register_script( 'jcore-broiler', '', array(), '1', false );
		wp_enqueue_script( 'jcore-broiler' );
		wp_localize_script(
			'jquery',
			'jcoreBroiler',
			array(
				'api_urls' => $api_urls,
				'nonce'    => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	/**
	 * Adds allowed origins.
	 *
	 * @param array $origins Array of allowed origins.
	 * @return array
	 */
	public static function add_allowed_origins( array $origins ): array {
		return array_merge(
			$origins,
			array(
				'http://127.0.0.1:5173',
				'http://localhost:5173',
			)
		);
	}

	/**
	 * Sends CORS headers.
	 *
	 * @param mixed $value Unused here.
	 * @return mixed
	 */
	public static function send_allowed_origin( mixed $value ): mixed {
		if ( is_allowed_http_origin() ) {
			header( 'Access-Control-Allow-Origin: ' . get_http_origin() );
			header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
			header( 'Access-Control-Allow-Credentials: true' );
		}
		return $value;
	}
}
