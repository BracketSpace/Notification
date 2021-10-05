<?php
/**
 * User Queries
 *
 * @package notification
 */

namespace BracketSpace\Notification\Queries;

use BracketSpace\Notification\Utils\Cache\ObjectCache;

/**
 * Users Queries class
 */
class UserQueries {

	/**
	 * Gets all users.
	 *
	 * @return array<int,array{ID: string, user_email: string, display_name: string}>
	 */
	public static function all() {
		global $wpdb;

		$cache = new ObjectCache( 'users', 'notification' );

		$users = $cache->get();

		if ( empty( $users ) ) {
			$users = $wpdb->get_results( "SELECT ID, user_email, display_name FROM $wpdb->users", 'ARRAY_A' ); //phpcs:ignore
			$cache->set( $users );
		}

		return $users;
	}

	/**
	 * Gets users with role.
	 *
	 * @param string $role user role.
	 * @return array<int,array{ID: string, user_email: string, display_name: string}>
	 */
	public static function with_role( string $role ) {
		global $wpdb;

		$cache = new ObjectCache( $role . '_users', 'notification' );

		$users = $cache->get();

		if ( empty( $users ) ) {
			$users = $wpdb->get_results( //phpcs:ignore
				$wpdb->prepare( "SELECT u.ID, u.user_email, u.display_name FROM $wpdb->users AS u INNER JOIN $wpdb->usermeta AS m ON u.ID = m.user_id WHERE m.meta_key = '{$wpdb->get_blog_prefix()}capabilities' AND m.meta_value LIKE %s", '%\"' . $wpdb->esc_like( $role ) . '\"%' ),
				'ARRAY_A'
			);
			$cache->set( $users );
		}

		return $users;
	}

}
