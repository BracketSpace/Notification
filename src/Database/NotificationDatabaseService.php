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
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;
use BracketSpace\Notification\Store\Notification as NotificationStore;

/**
 * This class describes a notification database service.
 *
 * @since 9.0.0
 */
class NotificationDatabaseService
{
	/**
	 * Indicates whether an operation is in progress.
	 *
	 * Returns string with the name of the operation
	 * or false if no operation is in progress.
	 *
	 * @var false|string
	 */
	protected static $doingOperation = false;

	/**
	 * Last ID of the post that has been created or updated.
	 *
	 * @var int
	 */
	protected static $lastUpsertedPostId = 0;

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
	 * Checks whether save process is in progress.
	 *
	 * @return false|string
	 */
	public static function doingOperation()
	{
		return self::$doingOperation;
	}

	/**
	 * Gets last upserted Post ID.
	 *
	 * @return int
	 */
	public static function getLastUpsertedPostId(): int
	{
		return self::$lastUpsertedPostId;
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

		return get_page_by_path($hash, OBJECT, 'notification');
	}

	/**
	 * Upserts the Notification database entry.
	 *
	 * @param \BracketSpace\Notification\Core\Notification $notification The notification
	 * @return void
	 */
	public static function upsert(Notification $notification)
	{
		self::$doingOperation = 'upsert';

		/**
		 * This action has been moved from Admin\PostType::save()
		 */
		do_action('notification/data/save', $notification);

		// Get information about created_at currently saved in db
		$createdAt = DatabaseService::db()->get_var(
			DatabaseService::db()->prepare(
				'SELECT created_at FROM %i WHERE hash = %s',
				self::getNotificationsTableName(),
				$notification->getHash()
			)
		);

		// Insert main notification object.
		DatabaseService::db()->replace(
			self::getNotificationsTableName(),
			[
				'hash' => $notification->getHash(),
				'title' => $notification->getTitle(),
				'trigger_slug' => $notification->getTrigger() ? $notification->getTrigger()->getSlug() : '',
				'enabled' => $notification->isEnabled(),
				'created_at' => $createdAt ?? current_time('mysql', true),
				'updated_at' => current_time('mysql', true),
			],
			[
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
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
					'updated_at' => current_time('mysql', true),
				],
				[
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
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
					'updated_at' => current_time('mysql', true),
				]
			);
		}

		// Handle corresponding WP Post entry
		$post = self::notificationToPost($notification);

		$postData = apply_filters(
			'notification/data/saving/post',
			[
				'ID' => $post === null ? 0 : $post->ID,
				'post_title' => $notification->getTitle(),
				'post_name' => $notification->getHash(),
				/**
				 * @todo Remove backward compatibility content save.
				 */
				'post_content' => $notification->to('json'),
				'post_status' => $notification->isEnabled() ? 'publish' : 'draft',
				'post_type' => 'notification',
			]
		);

		self::$lastUpsertedPostId = wp_insert_post($postData);

		/**
		 * These actions has been moved from Admin\PostType::save()
		 */
		do_action_deprecated('notification/data/save/after', [$notification], '9.0.0', 'notification/data/saved');
		do_action('notification/data/saved', $notification);

		static::getCache($notification->getHash())->delete();

		self::$doingOperation = false;
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
		$cache = static::getCache($hash);

		$notificationData = $cache->get();

		if (!is_array($notificationData)) {
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

			// Set version based on creation or last update date.
			$versionDate = $notificationData['updated_at'] ?? $notificationData['created_at'] ?? 'now';
			$notificationData['version'] = strtotime($versionDate);

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

			$cache->set($notificationData);
		}

		return Notification::from('array', $notificationData);
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
		self::$doingOperation = 'delete';

		DatabaseService::db()->delete(
			self::getNotificationsTableName(),
			[
				'hash' => $hash,
			]
		);

		self::deleteCarriers($hash);
		self::deleteExtras($hash);

		$post = self::notificationToPost($hash);
		if ($post instanceof \WP_Post) {
			wp_delete_post($post->ID, true);
		}

		static::getCache($hash)->delete();

		self::$doingOperation = false;
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

	/**
	 * Gets the cache instance for single notification.
	 *
	 * @param string $hash Notification hash.
	 * @return CacheDriver\ObjectCache
	 */
	protected static function getCache($hash)
	{
		$cache = new CacheDriver\ObjectCache('notification');
		$cache->set_key(static::getCacheKey($hash));

		return $cache;
	}

	/**
	 * Gets the cache key for single notification.
	 *
	 * @param string $hash Notification hash.
	 * @return string
	 */
	protected static function getCacheKey($hash)
	{
		return sprintf('notification-%s', $hash);
	}
}
