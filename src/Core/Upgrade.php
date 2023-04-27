<?php

/**
 * Upgrade class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Utils\WpObjectHelper;
use BracketSpace\Notification\Store;
use BracketSpace\Notification\Queries\NotificationQueries;

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
	public static $dataVersion = 2;

	/**
	 * Version of database tables
	 *
	 * @var int
	 */
	public static $dbVersion = 1;

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
	 * @action admin_init
	 *
	 * @return void
	 * @since  6.0.0
	 */
	public function checkUpgrade()
	{
		$dataVersion = get_option(
			static::$dataSettingName,
			0
		);

		if ($dataVersion >= static::$dataVersion) {
			return;
		}

		while ($dataVersion < static::$dataVersion) {
			$dataVersion++;
			$upgradeMethod = 'upgrade_to_v' . $dataVersion;

			if (
				!method_exists(
					$this,
					$upgradeMethod
				)
			) {
				continue;
			}

			call_user_func([$this, $upgradeMethod]);
		}

		update_option(
			static::$dataSettingName,
			static::$dataVersion
		);
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

		global $wpdb;

		$charsetCollate = '';

		if (!empty($wpdb->charset)) {
			$charsetCollate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}

		if (!empty($wpdb->collate)) {
			$charsetCollate .= " COLLATE {$wpdb->collate}";
		}

		$logsTable = $wpdb->prefix . 'notification_logs';

		$sql = "
		CREATE TABLE {$logsTable} (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			type text NOT NULL,
			time_logged timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			message text NOT NULL,
			component text NOT NULL,
			UNIQUE KEY ID (ID)
		) $charsetCollate;
		";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta($sql);

		update_option(
			static::$dbSettingName,
			static::$dbVersion
		);
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
		$enabledCarriers = (array)get_post_meta(
			$postId,
			'_enabled_notification',
			false
		);

		if (
			in_array(
				$carrier->getSlug(),
				$enabledCarriers,
				true
			)
		) {
			$carrier->enable();
		} else {
			$carrier->disable();
		}

		// Set data.
		$data = get_post_meta(
			$postId,
			'_notification_type_' . $carrier->getSlug(),
			true
		);
		$fieldValues = apply_filters_deprecated(
			'notification/notification/form_fields/values',
			[$data, $carrier],
			'6.0.0',
			'notification/carrier/fields/values'
		);
		$fieldValues = apply_filters(
			'notification/carrier/fields/values',
			$fieldValues,
			$carrier
		);

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
		$notifications = NotificationQueries::all(true);
		foreach ($notifications as $adapter) {
			$post = $adapter->getPost();

			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			$adapter->setHash($post->post_name);
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			$adapter->setTitle($post->post_title);

			// Trigger.
			$triggerSlug = get_post_meta(
				$adapter->getId(),
				'_trigger',
				true
			);
			$trigger = Store\Trigger::get($triggerSlug);

			if (!empty($trigger)) {
				$adapter->setTrigger($trigger);
			}

			// Carriers.
			$rawCarriers = (array)Store\Carrier::all();
			$carriers = [];

			foreach ($rawCarriers as $carrier) {
				if (empty($carrier)) {
					continue;
				}

				$carriers[$carrier->getSlug()] = $this->populateCarrier(
					clone $carrier,
					$adapter->getId()
				);
			}

			if (!empty($carriers)) {
				$adapter->setCarriers($carriers);
			}

			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			$adapter->setEnabled($post->post_status === 'publish');
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			$adapter->setVersion($post->post_modified_gmt);

			$adapter->save();
		}

		// 2. Delete trashed Notifications.
		$trashedNotifications = get_posts(
			[
				'post_type' => 'notification',
				'posts_per_page' => -1,
				'post_status' => 'trash',
			]
		);
		foreach ($trashedNotifications as $trashedNotification) {
			wp_delete_post(
				$trashedNotification->ID,
				true
			);
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
		global $wpdb;

		// 1. Changes the Trigger slugs.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$notifications = $wpdb->getResults(
			"SELECT p.ID, p.post_content
			FROM {$wpdb->posts} p
			WHERE p.post_type = 'notification'"
		);

		foreach ($notifications as $notifiationRaw) {
			$data = json_decode(
				$notifiationRaw->postContent,
				true
			);

			$data['trigger'] = preg_replace(
				array_keys($this->triggerSlugReplacements()),
				array_values($this->triggerSlugReplacements()),
				$data['trigger']
			);

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update(
				$wpdb->posts,
				[
					'post_content' => wp_json_encode(
						$data,
						JSON_UNESCAPED_UNICODE
					),
				],
				[
					'ID' => $notifiationRaw->ID,
				],
				['%s'],
				['%d']
			);
		}

		// 2. Changes the settings section `notifications` to `carriers`.

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update(
			$wpdb->options,
			['option_name' => 'notification_carriers'],
			['option_name' => 'notification_notifications'],
			['%s'],
			['%s']
		);
	}
}
