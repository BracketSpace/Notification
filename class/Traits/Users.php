<?php
/**
 * Trait for users database operations.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

use BracketSpace\Notification\Utils\Cache\ObjectCache;

/**
 * Users trait
 */
trait Users {

	/**
	 * Get all prepared users directly from database.
	 *
	 * @return array $results user results.
	 */
	public function get_all_users() {

		global $wpdb;

		$cache = new ObjectCache( 'users', 'notification' );

		$users = $cache->get();

		if ( empty( $users ) ) {
			$users = $wpdb->get_results( "SELECT ID, user_email, display_name FROM $wpdb->users" ); //phpcs:ignore
			$cache->set( $users );
		}

		return $users;

	}

	/**
	 * Get role based prepared users directly from database.
	 *
	 * @param string $role user role.
	 *
	 * @return array $results user results.
	 */
	public function get_users_by_role( $role ) {

		global $wpdb;

		$cache = new ObjectCache( $role . '_users', 'notification' );

		$users = $cache->get();

		if ( empty( $users ) ) {
			$users = $wpdb->get_results( //phpcs:ignore
				$wpdb->prepare( "SELECT u.ID, u.user_email, u.display_name FROM $wpdb->users AS u INNER JOIN $wpdb->usermeta AS m ON u.ID = m.user_id WHERE m.meta_key = '{$wpdb->get_blog_prefix()}capabilities' AND m.meta_value LIKE %s", '%\"' . $wpdb->esc_like( $role ) . '\"%' )
			);
			$cache->set( $users );
		}

		return $users;

	}

}
