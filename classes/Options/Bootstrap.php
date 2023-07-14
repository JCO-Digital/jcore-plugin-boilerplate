<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing
namespace JcoreBroiler\Options;

use Timber\Timber;

/**
 * Database class, handles bootstrapping the custom database tables.
 *
 * @package JcoreBroiler\Database
 */
class Bootstrap {

	/**
	 * Initializes the Options class.
	 * You can use OptionsPageBuilder to build options pages here or just leave it as it is.
	 *
	 * @see OptionsPageBuilder
	 * @return void
	 */
	public static function init(): void {
		self::timber_init();
	}

	/**
	 * Initializes Timber.
	 *
	 * @return void
	 */
	private static function timber_init(): void {
		Timber::$locations = array(
			plugin_dir_path( __FILE__ ) . '../../views',
		);
	}
}
