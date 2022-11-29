<?php

/**
 * SyncTable field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Utils\Settings\Fields;

use BracketSpace\Notification\Admin\PostType;
use BracketSpace\Notification\Core\Sync as CoreSync;
use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Queries\NotificationQueries;

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
		$wpJsonNotifiactions = PostType::getAllNotifications();
		$jsonNotifiactions = CoreSync::getAllJson();
		$collection = [];

		// Load the WP Notifications first.
		foreach ($wpJsonNotifiactions as $json) {
			try {
				$adapter = notification_adapt_from(
					'JSON',
					$json
				);
				$notification = $adapter->getNotification();
			} catch (\Throwable $e) {
				// Do nothing.
				continue;
			}

			/**
			 * @var \BracketSpace\Notification\Defaults\Adapter\WordPress|null
			 */
			$notificationAdapter = NotificationQueries::withHash($notification->getHash());

			if ($notificationAdapter === null) {
				continue;
			}

			$collection[$notification->getHash()] = [
				'source' => 'WordPress',
				'has_json' => false,
				'up_to_date' => false,
				'post_id' => $notificationAdapter->getId(),
				'notification' => $notification,
			];
		}

		// Compare against JSON.
		foreach ($jsonNotifiactions as $json) {
			try {
				$adapter = notification_adapt_from(
					'JSON',
					$json
				);
				$notification = $adapter->getNotification();
			} catch (\Throwable $e) {
				// Do nothing.
				continue;
			}

			if (isset($collection[$notification->getHash()])) {
				$collection[$notification->getHash()]['has_json'] = true;

				$wpNotification = $collection[$notification->getHash()]['notification'];

				if (
					version_compare(
						(string)$wpNotification->getVersion(),
						(string)$notification->getVersion(),
						'>='
					)
				) {
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
			if (!$data['up_to_date']) {
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
