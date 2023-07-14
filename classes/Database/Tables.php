<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing

namespace JcoreBroiler\Database;

/**
 * The tables class can be used to access and modify the database tables.
 *
 * @package TahtiPollo
 */
class Tables {


	/**
	 * Gets the charset collate. (WPDB method)
	 *
	 * @uses wpdb::get_charset_collate()
	 * @return string
	 */
	public static function get_charset_collate(): string {
		global $wpdb;
		return $wpdb->get_charset_collate();
	}

	/**
	 * Handles checking if the table has foreign keys.
	 *
	 * @param string   $table_name The table name to check.
	 * @param string[] $fk_keys The foreign keys to check, an array of strings.
	 *
	 * @return bool
	 */
	public static function has_foreign_keys( string $table_name, array $fk_keys ): bool {
		global $wpdb;
		$query        = $wpdb->prepare(
			"
			SELECT * FROM
				information_schema.TABLE_CONSTRAINTS
			WHERE
				CONSTRAINT_SCHEMA = DATABASE()
			AND
				TABLE_NAME = %s
			AND
				CONSTRAINT_TYPE = 'FOREIGN KEY'
		",
			$table_name
		);
		$foreign_keys = $wpdb->get_results(
			$query
		);
		$found_keys   = 0;
		foreach ( $foreign_keys as $key ) {
			if ( in_array( $key->CONSTRAINT_NAME, $fk_keys, true ) ) {
				++$found_keys;
			}
		}

		return $found_keys === count( $fk_keys );
	}

	/**
	 * Checks if a table exists.
	 *
	 * @param string $table_name The table name to check.
	 *
	 * @return bool
	 * @uses wpdb::get_var()
	 */
	public static function table_exists( string $table_name ): bool {
		global $wpdb;
		return $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) === $table_name;
	}
}
