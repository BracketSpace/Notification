<?php

/**
 * User Queries
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Queries;

use BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;

/**
 * Users Queries class
 */
class UserQueries
{

	/**
	 * Gets all users.
	 *
	 * @return array<int,array{ID: string, user_email: string, display_name: string}>
	 */
	public static function all()
	{
		$driver = new CacheDriver\ObjectCache('notification', 6 * HOUR_IN_SECONDS);
		$cache = new Cache($driver, 'users');

		return $cache->collect(
			static function () {
				global $wpdb;

				// We're using direct db call for performance purposes - we only need the post_content field.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
				return $wpdb->getResults("SELECT ID, user_email, display_name FROM $wpdb->users", 'ARRAY_A');
			}
		);
	}

	/**
	 * Gets users with role.
	 *
	 * @param string $role user role.
	 * @return array<int,array{ID: string, user_email: string, display_name: string}>
	 */
	public static function with_role( string $role )
	{
		$driver = new CacheDriver\ObjectCache('notification', 6 * HOUR_IN_SECONDS);
		$cache = new Cache($driver, sprintf('%s_users', $role));

		return $cache->collect(
			static function () use ( $role ) {
				global $wpdb;

				// We're using direct db call for performance purposes - we only need the post_content field.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				return $wpdb->getResults(
					$wpdb->prepare(
						"SELECT u.ID, u.user_email, u.display_name
					FROM $wpdb->users AS u
					INNER JOIN $wpdb->usermeta AS m ON u.ID = m.user_id
					WHERE m.meta_key = '{$wpdb->getBlogPrefix()}capabilities'
					AND m.meta_value LIKE %s",
						'%\"' . $wpdb->escLike($role) . '\"%'
					),
					'ARRAY_A'
				);
			}
		);
	}
}
