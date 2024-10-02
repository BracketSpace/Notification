<?php

/**
 * Notification Queries
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Database\Queries;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Database\NotificationDatabaseService as Db;

/**
 * Notification Queries class
 *
 * @deprecated 9.0.0
 */
class NotificationQueries
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		notification_deprecated_class( __CLASS__, '9.0.0' );
	}

	/**
	 * Gets Notification posts.
	 *
	 * @param bool $includingDisabled If should include disabled notifications as well.
	 * @return array<Notification>
	 * @since  8.0.0
	 */
	public static function all(bool $includingDisabled = false): array
	{
		$queryArgs = [
			'posts_per_page' => -1,
			'post_type' => 'notification',
		];

		if ($includingDisabled) {
			$queryArgs['post_status'] = ['publish', 'draft'];
		}

		// WPML compat.
		if (defined('ICL_LANGUAGE_CODE')) {
			$queryArgs['suppress_filters'] = 0;
		}

		$wpposts = get_posts($queryArgs);
		$posts = [];

		if (empty($wpposts)) {
			return $posts;
		}

		foreach ($wpposts as $wppost) {
			$notification = Db::postToNotification($wppost);

			if (!($notification instanceof Notification)) {
				continue;
			}

			$posts[] = $notification;
		}

		return $posts;
	}

	/**
	 * Gets Notification post by hash.
	 *
	 * @param string $hash Notification hash.
	 * @return ?Notification
	 * @since  8.0.0
	 */
	public static function withHash(string $hash)
	{
		$post = get_page_by_path($hash, OBJECT, 'notification');

		return empty($post) ? null : Db::postToNotification($post);
	}
}
