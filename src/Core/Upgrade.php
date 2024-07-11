<?php

/**
 * Upgrade class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Database\DatabaseService;
use BracketSpace\Notification\Database\NotificationDatabaseService;
use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Utils\WpObjectHelper;
use BracketSpace\Notification\Store;

/**
 * Upgrade class
 */
class Upgrade
{
	/**
	 * Current data version
	 *
	 * @var int
	 */
	public static $dataVersion = 3;

	/**
	 * Version of database tables
	 *
	 * @var int
	 */
	public static $dbVersion = 3;

	/**
	 * Data version setting key name
	 *
	 * @var string
	 */
	public static $dataSettingName = 'notification_data_version';

	/**
	 * Database version setting key name
	 *
	 * @var string
	 */
	public static $dbSettingName = 'notification_db_version';

	/**
	 * Checks if an upgrade is required
	 *
	 * @action notification/init 100
	 *
	 * @return void
	 * @since  6.0.0
	 */
	public function checkUpgrade()
	{
		$dataVersion = get_option(static::$dataSettingName, 0);

		if ($dataVersion >= static::$dataVersion) {
			return;
		}

		while ($dataVersion < static::$dataVersion) {
			$dataVersion++;
			$upgradeMethod = [$this, sprintf('upgradeToV%d', $dataVersion)];

			if (!method_exists($upgradeMethod[0], $upgradeMethod[1]) || !is_callable($upgradeMethod)) {
				continue;
			}

			call_user_func($upgradeMethod);
		}

		update_option(static::$dataSettingName, static::$dataVersion);
	}

	/**
	 * --------------------------------------------------
	 * Database.
	 * --------------------------------------------------
	 */

	/**
	 * Install database tables
	 *
	 * @action notification/init
	 * @return void
	 */
	public function upgradeDb()
	{
		$currentVersion = get_option(static::$dbSettingName);

		if ($currentVersion >= static::$dbVersion) {
			return;
		}

		$db = DatabaseService::db();

		$charsetCollate = '';

		if (!empty($db->charset)) {
			$charsetCollate = "DEFAULT CHARACTER SET {$db->charset}";
		}

		if (!empty($db->collate)) {
			$charsetCollate .= " COLLATE {$db->collate}";
		}

		$logsTable = DatabaseService::prefixTable('notification_logs');
		$notificationsTable = NotificationDatabaseService::getNotificationsTableName();
		$notificationCarriersTable = NotificationDatabaseService::getNotificationCarriersTableName();
		$notificationExtrasTable = NotificationDatabaseService::getNotificationExtrasTableName();

		$sql = "
		CREATE TABLE {$logsTable} (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			type text NOT NULL,
			time_logged timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			message text NOT NULL,
			component text NOT NULL,
			UNIQUE KEY ID (ID)
		) $charsetCollate;

		CREATE TABLE {$notificationsTable} (
			hash varchar(150) NOT NULL,
			title tinytext NOT NULL,
			trigger_slug varchar(250) NOT NULL,
			enabled tinyint(1) NOT NULL DEFAULT '0',
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (hash),
			UNIQUE KEY hash (hash)
		) $charsetCollate;

		CREATE TABLE {$notificationCarriersTable} (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			notification_hash varchar(150) NOT NULL,
			slug varchar(150) NOT NULL,
			data json NULL DEFAULT NULL,
			enabled tinyint(1) NULL DEFAULT '0',
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (ID),
			KEY notification_hash (notification_hash)
		) $charsetCollate;

		CREATE TABLE {$notificationExtrasTable} (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			notification_hash varchar(150) NOT NULL,
			slug varchar(150) NOT NULL,
			data json NULL DEFAULT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (ID),
			KEY notification_hash (notification_hash)
		) $charsetCollate;
		";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta($sql);

		update_option(static::$dbSettingName, static::$dbVersion);
	}

	/**
	 * --------------------------------------------------
	 * Helper methods.
	 * --------------------------------------------------
	 */

	/**
	 * Populates Carrier with field values pulled from meta
	 *
	 * @param string|\BracketSpace\Notification\Interfaces\Sendable $carrier Sendable object or Carrier slug.
	 * @param int $postId Notification post ID.
	 * @return \BracketSpace\Notification\Interfaces\Sendable
	 * @throws \Exception If Carrier hasn't been found.
	 * @since  6.0.0
	 */
	protected function populateCarrier($carrier, $postId)
	{
		if (!$carrier instanceof Interfaces\Sendable) {
			$carrier = Store\Carrier::get($carrier);
		}

		if (!$carrier) {
			throw new \Exception('Wrong Carrier slug');
		}

		// Set enabled state.
		$enabledCarriers = (array)get_post_meta($postId, '_enabled_notification', false);

		if (in_array($carrier->getSlug(), $enabledCarriers, true)) {
			$carrier->enable();
		} else {
			$carrier->disable();
		}

		// Set data.
		$data = get_post_meta($postId, '_notification_type_' . $carrier->getSlug(), true);
		$fieldValues = apply_filters('notification/carrier/fields/values', $data, $carrier);

		foreach ($carrier->getFormFields() as $field) {
			if (!isset($fieldValues[$field->getRawName()])) {
				continue;
			}

			$field->setValue($fieldValues[$field->getRawName()]);
		}

		return $carrier;
	}

	/**
	 * Gets new trigger slug replacements
	 *
	 * @return array<mixed>
	 * @since  7.0.0
	 */
	public function triggerSlugReplacements()
	{
		$taxonomies = '(' . implode(
			'|',
			array_keys(WpObjectHelper::getTaxonomies())
		) . ')';

		// phpcs:disable
		return [
			'/wordpress\/comment_(.*)_(added|approved|replied|spammed|trashed|unapproved|published)/' => 'comment/${1}/${2}',
			'/wordpress\/media_(added|trashed|updated)/' => 'media/${1}',
			"/wordpress\/{$taxonomies}\/(created|updated|deleted)/" => 'taxonomy/${1}/${2}',
			'/wordpress\/((?!.*(plugin|theme)).*)\/(updated|trashed|published|drafted|added|pending|scheduled)/' => 'post/${1}/${3}',
			'/wordpress\/user_(.*)/' => 'user/${1}',
			'/wordpress\/user\/(.*)/' => 'user/${1}',
			'/wordpress\/plugin\/(.*)/' => 'plugin/${1}',
			'/wordpress\/theme\/(.*)/' => 'theme/${1}',
		];
		// phpcs:enable
	}

	/**
	 * --------------------------------------------------
	 * Upgrader methods.
	 * --------------------------------------------------
	 */

	/**
	 * Upgrades data to v1.
	 * - 1. Saves the Notification cache in post_content field.
	 * - 2. Deletes trashed Notifications.
	 * - 3. Removes old debug log.
	 *
	 * @return void
	 * @since  6.0.0
	 */
	public function upgradeToV1()
	{
		// 1. Save the Notification cache in post_content field.
		// This portion of the updater is no longer maintained and requires manual action.

		// 2. Delete trashed Notifications.
		$trashedNotifications = get_posts(
			[
				'post_type' => 'notification',
				'posts_per_page' => -1,
				'post_status' => 'trash',
			]
		);
		foreach ($trashedNotifications as $trashedNotification) {
			wp_delete_post($trashedNotification->ID, true);
		}

		// 3. Remove old debug log
		delete_option('notification_debug_log');
	}

	/**
	 * Upgrades data to v2.
	 * - 1. Changes the Trigger slugs.
	 * - 2. Changes the settings section `notifications` to `carriers`.
	 *
	 * @return void
	 * @since  6.0.0
	 */
	public function upgradeToV2()
	{
		$db = DatabaseService::db();

		// 1. Changes the Trigger slugs.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$notifications = (array)$db->get_results(
			"SELECT p.ID, p.post_content
			FROM {$db->posts} p
			WHERE p.post_type = 'notification'"
		);

		foreach ($notifications as $notificationRaw) {
			// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			$data = strlen($notificationRaw->post_content) > 0
				? json_decode($notificationRaw->post_content, true)
				: null;
			// phpcs:enable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

			if (!is_array($data) || !is_string($data['trigger'] ?? null)) {
				continue;
			}

			$data['trigger'] = preg_replace(
				array_keys($this->triggerSlugReplacements()),
				array_values($this->triggerSlugReplacements()),
				$data['trigger']
			);

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$db->update(
				$db->posts,
				[
					'post_content' => wp_json_encode($data, JSON_UNESCAPED_UNICODE),
				],
				[
					'ID' => $notificationRaw->ID,
				],
				['%s'],
				['%d']
			);
		}

		// 2. Changes the settings section `notifications` to `carriers`.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$db->update(
			$db->options,
			['option_name' => 'notification_carriers'],
			['option_name' => 'notification_notifications'],
			['%s'],
			['%s']
		);
	}

	/**
	 * Upgrades data to v3.
	 * - 1. Moves the notifications to custom table.
	 *
	 * @since [Next]
	 * @return void
	 */
	public function upgradeToV3()
	{
		$db = DatabaseService::db();

		// 1. Moves the notifications to custom table.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$notifications = (array)$db->get_results(
			"SELECT p.ID, p.post_content
			FROM {$db->posts} p
			WHERE p.post_type = 'notification' AND p.post_content<>''"
		);

		foreach ($notifications as $notificationRaw) {
			try {
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
				$notification = Notification::from('json', $notificationRaw->post_content);
			} catch (\Throwable $e) {
				continue;
			}

			if (! $notification instanceof Notification) {
				continue;
			}

			NotificationDatabaseService::upsert($notification);
		}
	}
}
