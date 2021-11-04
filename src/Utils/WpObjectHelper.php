<?php
/**
 * WordPress Object Helper class
 *
 * Provides static methods used to easily get defined content type objects.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils;

use BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;

/**
 * WpObjectHelper class
 */
class WpObjectHelper {

	/**
	 * Gets post type object
	 *
	 * @since  8.0.0
	 * @param  string $post_type_slug Post type slug.
	 * @return \WP_Post_Type|null
	 */
	public static function get_post_type( $post_type_slug ) {
		return get_post_type_object( $post_type_slug );
	}

	/**
	 * Gets registered post types in slug => name format
	 *
	 * @since  8.0.0
	 * @param  array<mixed> $args Query args.
	 * @return array<string,string>
	 */
	public static function get_post_types( $args = [] ) : array {
		$post_types = [];
		foreach ( get_post_types( $args, 'objects' ) as $post_type ) {
			if ( ! $post_type instanceof \WP_Post_Type ) {
				continue;
			}

			$post_types[ $post_type->name ] = $post_type->labels->singular_name;
		}

		return $post_types;
	}

	/**
	 * Gets post type object name
	 *
	 * @since  8.0.0
	 * @param  string $post_type_slug Post type slug.
	 * @return string|null
	 */
	public static function get_post_type_name( $post_type_slug ) {
		$post_type = self::get_post_type( $post_type_slug );
		return $post_type->labels->singular_name ?? null;
	}

	/**
	 * Gets taxonomy object
	 *
	 * @since  8.0.0
	 * @param  string $taxonomy_slug Taxonomy slug.
	 * @return \WP_Taxonomy|null
	 */
	public static function get_taxonomy( $taxonomy_slug ) {
		$taxonomy = get_taxonomy( $taxonomy_slug );
		return $taxonomy ? $taxonomy : null;
	}

	/**
	 * Gets registered taxonomies in slug => name format
	 *
	 * @since  8.0.0
	 * @param  array<mixed> $args Query args.
	 * @return array<string,\WP_Taxonomy>
	 */
	public static function get_taxonomies( $args = [] ) : array {
		$taxonomies = [];

		foreach ( get_taxonomies( $args, 'objects' ) as $taxonomy ) {
			if ( 'post_format' === $taxonomy->name ) {
				continue;
			}

			$taxonomies[ $taxonomy->name ] = $taxonomy->labels->singular_name;
		}

		return $taxonomies;
	}

	/**
	 * Gets taxonomy object name
	 *
	 * @since  8.0.0
	 * @param  string $taxonomy_slug Taxonomy slug.
	 * @return string|null
	 */
	public static function get_taxonomy_name( $taxonomy_slug ) {
		$taxonomy = self::get_taxonomy( $taxonomy_slug );
		return $taxonomy->labels->singular_name ?? null;
	}

	/**
	 * Gets comment type name
	 *
	 * @since  8.0.0
	 * @param  string $comment_type_slug Comment type slug.
	 * @return string|null
	 */
	public static function get_comment_type_name( $comment_type_slug ) {
		$comment_types = self::get_comment_types();
		return $comment_types[ $comment_type_slug ] ?? null;
	}

	/**
	 * Gets comment types from database
	 *
	 * @since  8.0.0
	 * @return array<string,string>
	 */
	public static function get_comment_types() : array {
		$driver = new CacheDriver\ObjectCache( 'notification' );
		$cache  = new Cache( $driver, 'comment_types' );

		return $cache->collect( function () {
			global $wpdb;

			$comment_types = [
				'comment'   => __( 'Comment', 'notification' ),
				'pingback'  => __( 'Pingback', 'notification' ),
				'trackback' => __( 'Trackback', 'notification' ),
			];

			// There's no other way to get comment types and we're using the cache lib.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$db_types = $wpdb->get_col(
				"SELECT DISTINCT comment_type
				FROM $wpdb->comments
				WHERE 1=1"
			);

			foreach ( $db_types as $type ) {
				if ( ! isset( $comment_types[ $type ] ) ) {
					// Dynamically generated and translated name.
					$name = ucfirst( str_replace( [ '_', '-' ], ' ', $type ) );

					$comment_types[ (string) $type ] = __( $name );
				}
			}

			return $comment_types;
		} );
	}

}
