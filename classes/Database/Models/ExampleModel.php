<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName,Squiz.Commenting.ClassComment.Missing

namespace JcoreBroiler\Database\Models;

use JcoreBroiler\Database\Tables;

/**
 * Example model.
 * This is an example model, you can use this as a template for your own models.
 * You can also remove this file if you don't need it.
 *
 * @package JcoreBroiler\Database
 */
class ExampleModel extends Model {

	/**
	 * @inheritDoc
	 */
	public static function table_schema(): string {
		$table_name      = self::get_table_name();
		$charset_collate = Tables::get_charset_collate();

		return "CREATE TABLE $table_name (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(255) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
	}
}
