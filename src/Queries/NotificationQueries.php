<?php

/**
 * Notification Queries
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Queries;

/**
 * Notification Queries class
 */
class NotificationQueries
{

	/**
	 * Gets Notification posts.
	 *
	 * @param bool $includingDisabled If should include disabled notifications as well.
	 * @return array<int, \BracketSpace\Notification\Defaults\Adapter\WordPress>
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
			$posts[] = notification_adapt_from(
				'WordPress',
				$wppost
			);
		}

		return $posts;
	}

	/**
	 * Gets Notification post by hash.
	 *
	 * @param string $hash Notification hash.
	 * @return \BracketSpace\Notification\Interfaces\Adaptable|null
	 * @since  8.0.0
	 */
	public static function withHash(string $hash)
	{
		$post = get_page_by_path(
			$hash,
			OBJECT,
			'notification'
		);

		return empty($post)
			? null
			: notification_adapt_from(
				'WordPress',
				$post
			);
	}
}
