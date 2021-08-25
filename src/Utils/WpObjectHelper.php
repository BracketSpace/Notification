<?php
/**
 * WordPress Object Helper class
 *
 * Provides static methods used to easily get defined content type objects.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils;

/**
 * WpObjectHelper class
 */
class WpObjectHelper {

	/**
	 * Gets post type object
	 *
	 * @since  [Next]
	 * @param  string $post_type_slug Post type slug.
	 * @return \WP_Post_Type|null
	 */
	public static function get_post_type( $post_type_slug ) : ?\WP_Post_Type {
		return get_post_type_object( $post_type_slug );
	}

	/**
	 * Gets post type object name
	 *
	 * @since  [Next]
	 * @param  string $post_type_slug Post type slug.
	 * @return string|null
	 */
	public static function get_post_type_name( $post_type_slug ) : ?string {
		$post_type = self::get_post_type( $post_type_slug );
		return $post_type->labels->singular_name ?? null;
	}

	/**
	 * Gets taxonomy object
	 *
	 * @since  [Next]
	 * @param  string $taxonomy_slug Taxonomy slug.
	 * @return \WP_Taxonomy|null
	 */
	public static function get_taxonomy( $taxonomy_slug ) : ?\WP_Taxonomy {
		$taxonomy = get_taxonomy( $taxonomy_slug );
		return $taxonomy ? $taxonomy : null;
	}

	/**
	 * Gets taxonomy object name
	 *
	 * @since  [Next]
	 * @param  string $taxonomy_slug Taxonomy slug.
	 * @return string|null
	 */
	public static function get_taxonomy_name( $taxonomy_slug ) : ?string {
		$taxonomy = self::get_taxonomy( $taxonomy_slug );
		return $taxonomy->labels->singular_name ?? null;
	}

	/**
	 * Gets comment type name
	 *
	 * @since  [Next]
	 * @param  string $comment_type_slug Comment type slug.
	 * @return string
	 */
	public static function get_comment_type_name( $comment_type_slug ) : string {
		$known_comment_types = [
			'comment'   => __( 'Comment', 'notification' ),
			'pingback'  => __( 'Pingback', 'notification' ),
			'trackback' => __( 'Trackback', 'notification' ),
		];

		if ( isset( $known_comment_types[ $comment_type_slug ] ) ) {
			return $known_comment_types[ $comment_type_slug ];
		}

		// Dynamically generated and translated name.
		return __( ucfirst( str_replace( [ '_', '-' ], ' ', $comment_type_slug ) ) );
	}

}
