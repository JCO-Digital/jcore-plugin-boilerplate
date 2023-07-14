<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing
namespace JcoreBroiler\Database;

use JcoreBroiler\Database\Models\ModelInterface;
use JcoreBroiler\StellarWP\DB\DB;

/**
 * Database class, handles bootstrapping the custom database tables.
 *
 * @package JcoreBroiler\Database
 */
class Bootstrap {

	/**
	 * Array of all models that should be initialized.
	 *
	 * @var ModelInterface[]
	 */
	private static array $models = array();

	/**
	 * Initializes the DB helper class.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action(
			'plugins_loaded',
			static function() {
				DB::init();
			},
			0
		);
	}

	/**
	 * Generates the required database tables if they don't exist.
	 *
	 * @uses dbDelta()
	 *
	 * @return void
	 */
	public static function create_tables(): void {
		foreach ( self::$models as $model ) {
			$model::init();
		}
	}
}
