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

		return $wpdb->prefix . 'tahtipollo_' . strtolower( basename( str_replace( '\\', '/', static::class ) ) );
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
	public static function init(): void {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( static::table_schema() );
	}

	/**
	 * @inheritDoc
	 */
	public static function get_all(): array {
		$query = DB::table( DB::raw( static::get_table_name() ) );
		return static::filter_query( 'all', $query )->getAll( 'OBJECT' ) ?? array();
	}

	/**
	 * @inheritDoc
	 */
	public static function get_by_id( int $id ): ?object {
		$query = DB::table( DB::raw( static::get_table_name() ) )
					->where( 'id', $id )
					->limit( 1 );
		return static::filter_query( 'by_id', $query )
					->get( 'OBJECT' );
	}

	/**
	 * @inheritDoc
	 */
	public static function exists( int $id ): bool {
		return ! empty( static::get_by_id( $id ) );
	}


	/**
	 * @inheritDoc
	 */
	public static function delete( array $args ): bool|int {
		if ( ! isset( $args['id'] ) ) {
			return false;
		}
		// The initial query.
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
}
