<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing

namespace JcoreBroiler\RestAPI;

/**
 * Helper class for the REST API.
 *
 * @package JcoreBroiler\RestAPI
 */
class RestAPIHelpers {

	/**
	 * Checks if the user is logged in.
	 *
	 * @return bool
	 */
	public static function is_user_logged_in(): bool {
		return is_user_logged_in();
	}
}
