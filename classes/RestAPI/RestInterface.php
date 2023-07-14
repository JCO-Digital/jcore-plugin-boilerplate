<?php

namespace JcoreBroiler\RestAPI;

/**
 * Classes RestAPI class, registers the REST API endpoints for classes and handles the requests.
 *
 * @package JcoreBroiler\RestAPI
 */
interface RestInterface {

	/**
	 * Initializes the REST API.
	 *
	 * @return void
	 */
	public static function init(): void;

	/**
	 * Registers the REST API endpoints.
	 *
	 * @return void
	 */
	public static function register_endpoints(): void;
}
