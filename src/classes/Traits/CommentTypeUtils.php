<?php
/**
 * Comment Type utilities.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

/**
 * CommentTypeUtils trait
 */
trait CommentTypeUtils {

	/**
	 * Gets nice, translated comment type
	 *
	 * @since  7.0.0
	 * @return string
	 */
	public function get_current_comment_type_name() {
		return self::get_comment_type_name( $this->get_comment_type() );
	}

	/**
	 * Gets nice, translated post name for post type slug
	 *
	 * @since  7.0.0
	 * @param  string $comment_type Comment type slug.
	 * @return string               Comment type name.
	 */
	public static function get_comment_type_name( $comment_type ) {
		$comment_types = notification_cache( 'comment_types' );
		return $comment_types[ $comment_type ] ?? '';
	}

	/**
	 * Gets comment type slug
	 *
	 * @since  7.0.0
	 * @return string
	 */
	public function get_comment_type() {
		return $this->comment_type;
	}

}
