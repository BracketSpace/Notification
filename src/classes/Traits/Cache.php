<?php
/**
 * Trait with cache access.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

/**
 * Cache trait
 */
trait Cache {

	/**
	 * Gets nice, translated post name for post type slug
	 *
	 * @since  [Next]
	 * @param  string $post_type post type slug.
	 * @return string post name
	 */
	public static function get_post_type_name( $post_type ) {
		$post_types = notification_cache( 'post_types' );
		return $post_types[ $post_type ] ?? '';
	}

	/**
	 * Gets nice, translated post name
	 *
	 * @since  [Next]
	 * @return string post name
	 */
	public function get_current_post_type_name() {
		return self::get_post_type_name( $this->get_post_type() );
	}

	/**
	 * Gets post type slug
	 *
	 * @since  [Next]
	 * @return string post type slug
	 */
	public function get_post_type() {
		return $this->post_type;
	}

}
