<?php

/**
 * Synchronization
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Core\Sync as CoreSync;
use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Database\NotificationDatabaseService;
use BracketSpace\Notification\Utils\Settings\CoreFields;
use BracketSpace\Notification\Utils\Settings\Fields as SpecificFields;
use BracketSpace\Notification\Dependencies\Micropackage\Ajax\Response;

/**
 * Sync class
 */
class Sync
{
	/**
	 * Registers synchronization settings
	 * Hooks into the Import / Export settings.
	 *
	 * @action notification/settings/register 50
	 *
	 * @param \BracketSpace\Notification\Utils\Settings $settings Settings API object.
	 * @return void
	 */
	public function settings($settings)
	{
		$importExport = $settings->addSection(__('Import / Export', 'notification'), 'import_export');
		$syncGroup = $importExport->addGroup(__('Synchronization', 'notification'), 'sync');

		$syncGroup->description('Synchronization allow to export or load the Notifications from JSON files.');

		$syncGroup->addField(
			[
				'name' => __('Actions', 'notification'),
				'slug' => 'actions',
				'addons' => [
					'message' => [$this, 'templateActions'],
				],
				'render' => [new CoreFields\Message(), 'input'],
				'sanitize' => [new CoreFields\Message(), 'sanitize'],
				'description' => __('Bulk actions for the table below.'),
			]
		);

		if (!CoreSync::isSyncing()) {
			return;
		}

		$syncGroup->addField(
			[
				'name' => __('Notifications', 'notification'),
				'slug' => 'notifications',
				'render' => [new SpecificFields\SyncTable(), 'input'],
				'sanitize' => '__return_null',
			]
		);
	}

	/**
	 * Gets the actions template
	 *
	 * @return string
	 * @since  6.0.0
	 */
	public function templateActions()
	{
		if (!CoreSync::isSyncing()) {
			return Templates::get('sync/disabled');
		}

		return Templates::get('sync/actions');
	}

	/**
	 * Synchronizes the Notification
	 *
	 * @action wp_ajax_notification_sync
	 *
	 * @return void
	 */
	public function ajaxSync()
	{
		check_ajax_referer('notification_csrf');

		$ajax = new Response();
		$data = $_POST;

		$callable = [$this, 'loadNotificationTo' . ucfirst($data['type'])];

		$response = method_exists($callable[0], $callable[1]) && is_callable($callable)
			? call_user_func($callable, $data['hash'])
			: false;

		if ($response === false) {
			$ajax->error(
				__('Something went wrong while importing the Notification, please refresh the page and try again.')
			);
		}

		$ajax->send($response);
	}

	/**
	 * Loads the Notification to JSON
	 *
	 * @param string $hash Notification hash.
	 * @return void
	 * @since  6.0.0
	 */
	public function loadNotificationToJson($hash)
	{
		$notification = NotificationDatabaseService::get($hash);

		if ($notification === null) {
			return;
		}

		CoreSync::saveLocalJson($notification);
	}

	/**
	 * Loads the Notification to JSON
	 *
	 * @param string $hash Notification hash.
	 * @return mixed
	 * @since  6.0.0
	 */
	public function loadNotificationToWordpress($hash)
	{
		$jsonNotifications = CoreSync::getAllJson();

		foreach ($jsonNotifications as $json) {
			try {
				$notification = Notification::from('json', $json);

				if ($notification->getHash() === $hash) {
					NotificationDatabaseService::upsert($notification);

					return get_edit_post_link(NotificationDatabaseService::getLastUpsertedPostId(), 'admin');
				}
			} catch (\Throwable $e) {
				// Do nothing.
				return false;
			}
		}
	}
}
