<?php
/**
 * Trait for database operations.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

use BracketSpace\Notification\Utils\Cache\ObjectCache;

/**
 * Database trait
 */
trait Database {

	/**
	 * Get all prepared users directly from database.
	 *
	 * @return array $results user results.
	 */
	public function get_all_users_from_db() {
		global $wpdb;

		$db_query    = "SELECT ID, user_email, display_name FROM {$wpdb->prefix}users";
		$results     = $wpdb->get_results( $db_query ); //phpcs:ignore
		$users_cache = new ObjectCache( 'cached_users_list', 'users' );

		$users_cache->set( $results );

		return $users_cache;
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

		$db_query            = "SELECT u.ID, u.user_email, u.display_name FROM {$wpdb->prefix}users AS u INNER JOIN {$wpdb->prefix}usermeta AS m ON u.ID = m.user_id WHERE m.meta_key = 'wp_capabilities' AND m.meta_value LIKE '%\"$role\"%'";
		$results             = $wpdb->get_results( $db_query ); //phpcs:ignore
		$users_by_role_cache = new ObjectCache( 'cached_users_list_by_role', 'users_by_role' );

		$users_by_role_cache->set( $results );

		return $users_by_role_cache;
	}
}
