<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing

namespace JcoreBroiler\Database\Models;

use JcoreBroiler\StellarWP\DB\QueryBuilder\QueryBuilder;

/**
 * The model interface implements the basic methods for the models.
 *
 * TODO: Add all needed interface methods, like get_by_id and so on.
 *
 * @package JcoreBroiler\Database\Models
 */
interface ModelInterface {
	/**
	 * Returns the table name.
	 *
	 * @return string
	 */
	public static function get_table_name(): string;

	/**
	 * Returns the hook prefix for the model.
	 *
	 * @return string
	 */
	public static function get_hook_prefix(): string;


	/**
	 * Returns a key to be used for the cache.
	 *
	 * @param string|int $key A key to be used for the cache.
	 *
	 * @return string
	 */
	public static function get_cache_key( string|int $key ): string;

	/**
	 * Returns the query builder with the filters applied.
	 *
	 * @param string       $query_name The query name to use for the filters.
	 * @param QueryBuilder $query The query builder to pass through filters.
	 *
	 * @return QueryBuilder
	 */
	public static function filter_query( string $query_name, QueryBuilder $query ): QueryBuilder;


	/**
	 * Returns the hook name for the model.
	 *
	 * @param string $query_name The query name to get the hook name for.
	 *
	 * @return string
	 */
	public static function get_hook_name( string $query_name ): string;

	/**
	 * Returns an array of all data.
	 *
	 * @param array $args An argument array, will be passed to finalize_query.
	 * @see finalize_query
	 *
	 * @return array
	 */
	public static function get_all( array $args ): array;

	/**
	 * Get a single item by id.
	 *
	 * @param int   $id The id of the item to get.
	 * @param array $args An argument array, will be passed to finalize_query.
	 *
	 * @see finalize_query
	 *
	 * @return null|object
	 */
	public static function get_by_id( int $id, array $args ): ?object;

	/**
	 * Checks if an item exists.
	 *
	 * @param int   $id The id of the item to check.
	 * @param array $args An argument array, will be passed to finalize_query.
	 *
	 * @see finalize_query
	 *
	 * @return bool
	 */
	public static function exists( int $id, array $args ): bool;

	/**
	 * Deletes an item by id.
	 *
	 * @param array $args An argument array, should contain the id of the item to delete.
	 *
	 * @return bool|int
	 */
	public static function delete( array $args ): bool|int;

	/**
	 * Should return a SQL query that creates the table.
	 *
	 * @return string
	 */
	public static function table_schema(): string;

	/**
	 * Initializes the model. (Registers the table)
	 *
	 * @return void
	 */
	public static function init(): void;

	/**
	 * This method is called after the query has been built it will call the query, and return the result.
	 *
	 * @param QueryBuilder $query The query builder to finalize.
	 * @param array        $args {
	 *           An array of arguments, that can be used to modify the query.
	 *           @type string|int $cache_key A key to be used for the cache.
	 *           @type bool $all Is this a multiselect query.
	 *           @type bool $cache Should the query be cached.
	 *           @type int $cache_time The time in seconds to cache the query.
	 * }
	 *
	 * @return mixed
	 */
	public static function finalize_query( QueryBuilder $query, array $args ): mixed;

	/**
	 * Updates a single row in the database.
	 *
	 * @param int   $id The id of the row to update.
	 * @param array $update_data The data to update.
	 *
	 * @return false|int
	 */
	public static function update( int $id, array $update_data ): false|int;
}
