<?php
/**
 * Post Type utilities.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

/**
 * PostTypeUtils trait
 */
trait PostTypeUtils {

	/**
	 * Gets nice, translated post name for post type slug
	 *
	 * @since  7.0.0
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
	 * @since  7.0.0
	 * @return string post name
	 */
	public function get_current_post_type_name() {
		return self::get_post_type_name( $this->get_post_type() );
	}

	/**
	 * Gets post type slug
	 *
	 * @since  7.0.0
	 * @return string post type slug
	 */
	public function get_post_type() {
		return $this->post_type;
	}

}
