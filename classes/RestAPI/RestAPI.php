<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing

namespace JcoreBroiler\RestAPI;

/**
 * Abstract class for all REST API classes.
 *
 * @package JcoreBroiler\RestAPI
 */
abstract class RestAPI implements RestInterface {

	/**
	 * Override this to get another namespace.
	 *
	 * @var string
	 */
	public static string $namespace = 'tahtipollo/v1';

	/**
	 * Gets a "nice" name of the class.
	 *
	 * @return string
	 */
	public static function nice_name(): string {
		return strtolower( basename( str_replace( '\\', '/', static::class ) ) );
	}

	/**
	 * @inheritDoc
	 */
	public static function init(): void {
		add_action( 'rest_api_init', array( static::class, 'register_endpoints' ) );
	}
}
