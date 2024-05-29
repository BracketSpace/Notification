<?php

/**
 * Notification Database Service.
 *
 * This class handles both wp_notification* tables and wp_posts table in sync.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Database;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Store\Notification as NotificationStore;
use function BracketSpace\Notification\convertNotificationData;

/**
 * This class describes a notification database service.
 *
 * @since [Next]
 */
class NotificationDatabaseService
{
	/**
	 * Gets the notifications table name.
	 *
	 * @return string The notifications table name.
	 */
	public static function getNotificationsTableName(): string
	{
		return DatabaseService::prefixTable('notifications');
	}

	/**
	 * Gets the notification carriers table name.
	 *
	 * @return string The notification carriers table name.
	 */
	public static function getNotificationCarriersTableName(): string
	{
		return DatabaseService::prefixTable('notification_carriers');
	}

	/**
	 * Gets the notification extras table name.
	 *
	 * @return string The notification extras table name.
	 */
	public static function getNotificationExtrasTableName(): string
	{
		return DatabaseService::prefixTable('notification_extras');
	}

	/**
	 * Counts the Notifications in database
	 *
	 * @return int
	 */
	public static function count(): int
	{
		return (int)DatabaseService::db()->get_var(
			sprintf('SELECT COUNT(*) FROM %s', self::getNotificationsTableName())
		);
	}

	/**
	 * Translates post ID to Notification object
	 *
	 * @param int|\WP_Post $post Notification post object or post ID
	 * @return ?Notification
	 */
	public static function postToNotification($post): ?Notification
	{
		$hash = get_post_field('post_name', $post, 'raw');

		return NotificationStore::has($hash) ? NotificationStore::get($hash) : null;
	}

	/**
	 * Translates Notification to WP_Post
	 *
	 * @param string|Notification $notification Notification object or hash.
	 * @return ?\WP_Post
	 */
	public static function notificationToPost($notification): ?\WP_Post
	{
		$hash = $notification instanceof Notification ? $notification->getHash() : $notification;

		return get_page_by_path($hash, OBJECT, 'post');
	}

	/**
	 * Upserts the Notification database entry.
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notification The notification
	 * @return void
	 */
	public static function upsert(Notification $notification)
	{
		/**
		 * This action has been moved from Admin\PostType::save()
		 */
		do_action('notification/data/save', $notification);

		// Insert main notification object.
		DatabaseService::db()->replace(
			self::getNotificationsTableName(),
			[
				'hash' => $notification->getHash(),
				'title' => $notification->getTitle(),
				'trigger_slug' => $notification->getTrigger() ? $notification->getTrigger()->getSlug() : '',
				'enabled' => $notification->isEnabled(),
			],
			[
				'%s',
				'%s',
				'%s',
				'%d',
			]
		);

		// Clear and insert carriers.
		self::deleteCarriers($notification->getHash());

		foreach ($notification->getCarriers() as $carrier) {
			DatabaseService::db()->replace(
				self::getNotificationCarriersTableName(),
				[
					'notification_hash' => $notification->getHash(),
					'slug' => $carrier->getSlug(),
					'data' => wp_json_encode($carrier->getData(), JSON_UNESCAPED_UNICODE),
					'enabled' => $carrier->isEnabled(),
				],
				[
					'%s',
					'%s',
					'%s',
					'%d',
				]
			);
		}

		// Clear and insert extras.
		self::deleteExtras($notification->getHash());

		foreach ($notification->getExtras() as $extraKey => $extraData) {
			DatabaseService::db()->replace(
				self::getNotificationExtrasTableName(),
				[
					'notification_hash' => $notification->getHash(),
					'slug' => $extraKey,
					'data' => wp_json_encode($extraData, JSON_UNESCAPED_UNICODE),
				]
			);
		}

		/**
		 * These actions has been moved from Admin\PostType::save()
		 */
		do_action_deprecated('notification/data/save/after', [$notification], '[Next]', 'notification/data/saved');
		do_action('notification/data/saved', $notification);
	}

	/**
	 * Checks if Notification exists in database.
	 *
	 * @param string $hash Notification hash
	 * @return bool
	 */
	public static function has($hash)
	{
		return (bool)DatabaseService::db()->get_var(
			DatabaseService::db()->prepare(
				'SELECT COUNT(*) FROM %i WHERE hash = %s',
				self::getNotificationsTableName(),
				$hash
			)
		);
	}

	/**
	 * Gets the Notification from database.
	 *
	 * @param string $hash Notification hash
	 * @return Notification|null
	 */
	public static function get($hash)
	{
		$notificationData = DatabaseService::db()->get_row(
			DatabaseService::db()->prepare(
				'SELECT * FROM %i WHERE hash = %s',
				self::getNotificationsTableName(),
				$hash
			),
			'ARRAY_A'
		);

		if ($notificationData === null) {
			return null;
		}

		$notificationData['trigger'] = $notificationData['trigger_slug'];

		$carriersDataRaw = DatabaseService::db()->get_results(
			DatabaseService::db()->prepare(
				'SELECT * FROM %i WHERE notification_hash = %s',
				self::getNotificationCarriersTableName(),
				$hash
			),
			'ARRAY_A'
		);

		$notificationData['carriers'] = array_reduce(
			(array)$carriersDataRaw,
			static function ($carriers, $data) {
				if (is_string($data['data'])) {
					$carriers[$data['slug']] = json_decode($data['data'], true);
				}
				return $carriers;
			},
			[]
		);

		$extrasDataRaw = DatabaseService::db()->get_results(
			DatabaseService::db()->prepare(
				'SELECT * FROM %i WHERE notification_hash = %s',
				self::getNotificationExtrasTableName(),
				$hash
			),
			'ARRAY_A'
		);

		$notificationData['extras'] = array_reduce(
			(array)$extrasDataRaw,
			static function ($extras, $data) {
				if (is_string($data['data'])) {
					$extras[$data['slug']] = json_decode($data['data'], true);
				}
				return $extras;
			},
			[]
		);

		return new Notification(convertNotificationData($notificationData));
	}

	/**
	 * Gets all the Notifications from database.
	 *
	 * @return array<string, Notification>
	 */
	public static function getAll()
	{
		$notifications = [];
		$notificationHashes = DatabaseService::db()->get_col(
			sprintf('SELECT hash FROM %s', self::getNotificationsTableName())
		);

		foreach ($notificationHashes as $hash) {
			$notification = self::get($hash);

			if (! $notification instanceof Notification) {
				continue;
			}

			$notifications[(string)$hash] = $notification;
		}

		return $notifications;
	}

	/**
	 * Deletes the Notification from database.
	 *
	 * @param string $hash Notification hash
	 * @return void
	 */
	public static function delete($hash)
	{
		DatabaseService::db()->delete(
			self::getNotificationsTableName(),
			[
				'hash' => $hash,
			]
		);

		self::deleteCarriers($hash);
		self::deleteExtras($hash);
	}

	/**
	 * Deletes the Notification carriers.
	 *
	 * @param string $hash Notification hash
	 * @return void
	 */
	public static function deleteCarriers($hash)
	{
		DatabaseService::db()->delete(
			self::getNotificationCarriersTableName(),
			[
				'notification_hash' => $hash,
			]
		);
	}

	/**
	 * Deletes the Notification extras.
	 *
	 * @param string $hash Notification hash
	 * @return void
	 */
	public static function deleteExtras($hash)
	{
		DatabaseService::db()->delete(
			self::getNotificationExtrasTableName(),
			[
				'notification_hash' => $hash,
			]
		);
	}
}
