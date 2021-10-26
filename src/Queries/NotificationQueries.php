<?php
/**
 * Notification Queries
 *
 * @package notification
 */

namespace BracketSpace\Notification\Queries;

use BracketSpace\Notification\Defaults\Adapter\WordPress as WordPressAdapter;

/**
 * Notification Queries class
 */
class NotificationQueries {

	/**
	 * Gets Notification posts.
	 *
	 * @since  8.0.0
	 * @param  bool $including_disabled If should include disabled notifications as well.
	 * @return array<int,WordPressAdapter>
	 */
	public static function all( bool $including_disabled = false ) : array {
		$query_args = [
			'posts_per_page' => -1,
			'post_type'      => 'notification',
		];

		if ( $including_disabled ) {
			$query_args['post_status'] = [ 'publish', 'draft' ];
		}

		// WPML compat.
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$query_args['suppress_filters'] = 0;
		}

		$wpposts = get_posts( $query_args );
		$posts   = [];

		if ( empty( $wpposts ) ) {
			return $posts;
		}

		foreach ( $wpposts as $wppost ) {
			$posts[] = notification_adapt_from( 'WordPress', $wppost );
		}

		return $posts;
	}

	/**
	 * Gets Notification post by hash.
	 *
	 * @since  8.0.0
	 * @param  string $hash Notification hash.
	 * @return \BracketSpace\Notification\Interfaces\Adaptable|null
	 */
	public static function with_hash( string $hash ) {
		$post = get_page_by_path( $hash, OBJECT, 'notification' );

		return empty( $post ) ? null : notification_adapt_from( 'WordPress', $post );
	}

}
