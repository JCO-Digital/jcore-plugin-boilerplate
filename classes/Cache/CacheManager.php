<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing

namespace JcoreBroiler\Cache;

/**
 * CacheManager class, handles the cache.
 *
 * @package JcoreBroiler\Cache
 */
class CacheManager {
	/**
	 * The cache registry key.
	 *
	 * @var string
	 */
	private static string $cache_registry_key = '_cache_registry';

	/**
	 * The group to use for the cache registry.
	 *
	 * @var string
	 */
	private static string $registry_group = 'cache_registry';

	/**
	 * Gets a value from the cache.
	 *
	 * @param mixed  $cache_key The cache key to fetch from.
	 * @param string $group The cache group to fetch from.
	 *
	 * @return mixed
	 */
	public static function get( mixed $cache_key, string $group = 'default' ): mixed {
		return wp_cache_get( $cache_key, $group );
	}

	/**
	 * Sets a value to the cache.
	 *
	 * @param mixed  $cache_key The cache key to set.
	 * @param mixed  $value The value to set.
	 * @param string $group The cache group to set to.
	 * @param int    $expiration The expiration time in seconds.
	 *
	 * @return bool
	 */
	public static function set( mixed $cache_key, mixed $value, string $group = 'default', int $expiration = 0 ): bool {
		static::update_cache_registry( $cache_key, $group, $expiration );
		return wp_cache_set( $cache_key, $value, $group, $expiration );
	}

	/**
	 * Updates the cache registry. (A list of all cache keys in a group)
	 *
	 * @param mixed  $cache_key The cache key to add to the registry.
	 * @param string $group The cache group to add to.
	 * @param int    $expiration The expiration time in seconds.
	 *
	 * @return void
	 */
	private static function update_cache_registry( mixed $cache_key, string $group, int $expiration ): void {
		$cache_registry = static::get_cache_registry();
		if ( ! isset( $cache_registry[ $group ] ) ) {
			$cache_registry[ $group ] = array();
		} else {
			// If we have a cache registry, we need to remove all expired keys.
			$cache_registry[ $group ] = array_filter(
				$cache_registry[ $group ],
				static function ( $timestamp ) {
					return $timestamp > time();
				}
			);
		}
		$expiry = time() + $expiration;
		if ( ! in_array( $cache_key, $cache_registry[ $group ], true )
			|| $cache_registry[ $group ][ $cache_key ] < $expiry ) {
			$cache_registry[ $group ][ $cache_key ] = $expiry;
		}
		static::set_cache_registry( $cache_registry );
	}

	/**
	 * Helper function to get the cache registry.
	 *
	 * @return mixed
	 */
	private static function get_cache_registry(): mixed {
		return static::get( self::$cache_registry_key, self::$registry_group );
	}

	/**
	 * Helper function to set the cache registry.
	 *
	 * @param mixed $cache_registry The cache registry to set, usually an array with the cache key.
	 *
	 * @return void
	 */
	private static function set_cache_registry( mixed $cache_registry ): void {
		wp_cache_set( self::$cache_registry_key, $cache_registry, self::$registry_group, 0 );
	}

	/**
	 * Clears the cache registry.
	 *
	 * @return void
	 */
	public static function clear_cache_registry(): void {
		static::set_cache_registry( array() );
	}

	/**
	 * Clears the cache for a single key.
	 *
	 * @param mixed  $cache_key The cache key to clear.
	 * @param string $group The cache group to clear.
	 */
	public static function clear_cache_key( mixed $cache_key, string $group = 'default' ): void {
		wp_cache_delete( $cache_key, $group );
	}

	/**
	 * Clears the cache for a group.
	 *
	 * @param string $group The cache group to clear.
	 *
	 * @return void
	 */
	public static function clear_cache_group( string $group ): void {
		$cache_registry = static::get_cache_registry();
		if ( isset( $cache_registry[ $group ] ) ) {
			foreach ( $cache_registry[ $group ] as $cache_key => $expire ) {
				wp_cache_delete( $cache_key, $group );
			}
		}
		unset( $cache_registry[ $group ] );
		static::set_cache_registry( $cache_registry );
	}
}
