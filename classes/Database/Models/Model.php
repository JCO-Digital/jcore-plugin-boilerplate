<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing

namespace JcoreBroiler\Database\Models;

use JcoreBroiler\StellarWP\DB\DB;
use JcoreBroiler\StellarWP\DB\QueryBuilder\QueryBuilder;

/**
 * The model class can be used to access and modify the database tables.
 *
 * @package JcoreBroiler\Database
 */
abstract class Model implements ModelInterface {


	/**
	 * @inheritDoc
	 */
	public static function get_table_name(): string {
		global $wpdb;

		return $wpdb->prefix . BROILER_TABLE_PREFIX . strtolower( basename( str_replace( '\\', '/', static::class ) ) );
	}

	/**
	 * Get the hook prefix for the model.
	 *
	 * @return string
	 */
	public static function get_hook_prefix(): string {
		return strtolower( basename( str_replace( '\\', '/', static::class ) ) );
	}

	/**
	 * @inheritDoc
	 */
	public static function get_cache_key( string|int $key ): string {
		return md5( static::get_hook_prefix() . '_' . $key );
	}

	/**
	 * @inheritDoc
	 */
	public static function init(): void {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( static::table_schema() );
	}

	/**
	 * @inheritDoc
	 */
	public static function get_all( array $args = array() ): array {
		$query = DB::table( DB::raw( static::get_table_name() ) );

		$args = wp_parse_args(
			$args,
			array(
				'all'       => true,
				'cache_key' => static::get_cache_key( 'all' ),
			)
		);

		return static::finalize_query(
			static::filter_query( 'all', $query ),
			$args
		) ?? array();
	}

	/**
	 * @inheritDoc
	 */
	public static function get_by_id( int $id, array $args = array() ): ?object {
		$query = DB::table( DB::raw( static::get_table_name() ) )
			->where( 'id', $id )
			->limit( 1 );

		$args = wp_parse_args(
			$args,
			array(
				'cache_key' => static::get_cache_key( 'single_' . $id ),
			)
		);

		return static::finalize_query( static::filter_query( 'by_id', $query ), $args );
	}

	/**
	 * @inheritDoc
	 */
	public static function exists( int $id, array $args = array() ): bool {
		return ! empty( static::get_by_id( $id, $args ) );
	}


	/**
	 * @inheritDoc
	 */
	public static function delete( array $args ): bool|int {
		if ( ! isset( $args['id'] ) ) {
			return false;
		}
		// Clear the cache and delete the item.
		wp_cache_delete( static::get_cache_key( 'all' ), static::get_hook_prefix() );
		wp_cache_delete( static::get_cache_key( 'single_' . $args['id'] ), static::get_hook_prefix() );
		return DB::table( DB::raw( static::get_table_name() ) )
			->where( 'id', $args['id'] )
			->delete();
	}

	/**
	 * @inheritDoc
	 */
	public static function filter_query( string $query_name, QueryBuilder $query ): QueryBuilder {
		return apply_filters( static::get_hook_name( $query_name ), $query );
	}

	/**
	 * @inheritDoc
	 */
	public static function get_hook_name( string $query_name ): string {
		return static::get_hook_prefix() . '_' . $query_name . '_query';
	}

	/**
	 * @inheritDoc
	 */
	public static function finalize_query( QueryBuilder $query, array $args = array() ): mixed {
		$args   = wp_parse_args(
			$args,
			array(
				'cache'      => true,
				'all'        => false,
				'cache_time' => apply_filters( static::get_hook_prefix() . '_cache_time', 5 * 60 ),
				'cache_key'  => md5( $query->getSQL() ),
			)
		);
		$cached = false;
		if ( $args['cache'] ) {
			// TODO: Think about a better implementation of the key, since this could be hard to reset later.
			// Check if there exists a cached value for the query.
			$cache_key = $args['cache_key'];
			$cached    = wp_cache_get( $cache_key, static::get_hook_prefix() );
		}
		if ( false !== $cached ) {
			return $cached;
		}
		// Otherwise we need to execute the query.
		if ( $args['all'] ) {
			$result = $query->getAll( 'OBJECT' );
		} else {
			$result = $query->get( 'OBJECT' );
		}

		if ( empty( $result ) ) {
			return $args['all'] ? array() : null;
		}

		if ( ! is_array( $result ) ) {
			$result = array( $result );
		}

		$result = array_map(
			function ( $item ) {
				// Go through all the items attributes and convert them to the correct type, using the class properties.
				foreach ( get_object_vars( $item ) as $key => $value ) {
					// We use the class properties to convert the values to the correct type.
					// This is done using $$ functionality, to get the property name, it can be a bit confusing.
					if ( property_exists( static::class, $key ) && is_callable( static::$$key ) ) {
						$item->$key = call_user_func( static::$$key, $value );
					}
				}
				return $item;
			},
			$result
		);
		// Unwrap the result if we only want a single item.
		if ( ! $args['all'] && ! empty( $result ) ) {
			$result = $result[0];
		}
		if ( $args['cache'] && isset( $cache_key ) ) {
			// Cache the result.
			wp_cache_set( $cache_key, $result, static::get_hook_prefix(), $args['cache_time'] );
		}

		return $result;
	}

	/**
	 * @inheritDoc
	 */
	public static function update( int $id, array $update_data ): false|int {
		$query = DB::table( DB::raw( static::get_table_name() ) )
			->where( 'id', $id );
		// Once again, since WPEngine does not allow to clear the cache by group we need to clear all the caches.
		wp_cache_delete( static::get_cache_key( 'single_' . $id ), static::get_hook_prefix() );
		wp_cache_delete( static::get_cache_key( 'all' ), static::get_hook_prefix() );
		do_action( static::get_hook_prefix() . '_update', $id, $update_data );
		return static::filter_query( 'update', $query )->update( $update_data );
	}
}
