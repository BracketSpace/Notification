<?php

/**
 * SyncTable field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\Fields;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Core\Sync as CoreSync;
use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Database\NotificationDatabaseService;

/**
 * SyncTable class
 */
class SyncTable
{
	/**
	 * Field markup.
	 *
	 * @param \BracketSpace\Notification\Utils\Settings\Field $field Field instance.
	 * @return void
	 */
	public function input($field)
	{
		// Get all Notifications.
		$wpNotifiactions = NotificationDatabaseService::getAll();
		$jsonNotifications = CoreSync::getAllJson();
		$collection = [];

		// Load the WP Notifications first.
		foreach ($wpNotifiactions as $notification) {
			$post = NotificationDatabaseService::notificationToPost($notification);

			if ($post === null) {
				continue;
			}

			$collection[$notification->getHash()] = [
				'source' => 'WordPress',
				'has_json' => false,
				'up_to_date' => false,
				'post_id' => $post->ID,
				'notification' => $notification,
			];
		}

		// Compare against JSON.
		foreach ($jsonNotifications as $json) {
			try {
				$notification = Notification::from('json', $json);
			} catch (\Throwable $e) {
				// Do nothing.
				continue;
			}

			if (isset($collection[$notification->getHash()])) {
				$collection[$notification->getHash()]['has_json'] = true;

				$wpNotification = $collection[$notification->getHash()]['notification'];

				if (version_compare((string)$wpNotification->getVersion(), (string)$notification->getVersion(), '>=')) {
					$collection[$notification->getHash()]['up_to_date'] = true;
				}
			} else {
				$collection[$notification->getHash()] = [
					'source' => 'JSON',
					'has_post' => false,
					'up_to_date' => false,
					'notification' => $notification,
				];
			}
		}

		// Filter synchronized.
		foreach ($collection as $key => $data) {
			if (! $data['up_to_date']) {
				continue;
			}

			unset($collection[$key]);
		}

		if (empty($collection)) {
			Templates::render('sync/notifications-empty');
			return;
		}

		Templates::render(
			'sync/notifications',
			[
				'collection' => array_reverse($collection),
			]
		);
	}
}
