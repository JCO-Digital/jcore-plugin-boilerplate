<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

namespace JcoreBroiler\Database\Posts;

use WP_Post;

/**
 * The class for the modules, helper functions to fetch modules, with mostly sane defaults.
 *
 * @package JcoreBroiler\Database\Posts
 */
class Modules {

	/**
	 * Fetches all the modules.
	 *
	 * @param array $args An array of arguments, passed to get_posts, has some defaults.
	 *
	 * @return WP_Post[] An array of WP_Post objects.
	 */
	public static function get_all( array $args = array() ): array {
		$args = wp_parse_args(
			$args,
			array(
				'post_type'      => 'module',
				'posts_per_page' => -1,
			)
		);

		return get_posts(
			$args
		);
	}

	/**
	 * Fetches all the modules of a certain category.
	 *
	 * @param string $category The slug of the category to fetch.
	 * @param array  $args An array of arguments, passed to get_posts, has some defaults.
	 * @return WP_Post[]
	 */
	public static function get_all_by_category( string $category, array $args = array() ): array {
		$args = wp_parse_args(
			$args,
			array(
				'post_type'      => 'module',
				'posts_per_page' => -1,
				'tax_query'      => array(
					array(
						'taxonomy' => 'module_category',
						'field'    => 'slug',
						'terms'    => $category,
					),
				),
			)
		);

		return get_posts(
			$args
		);
	}
}
