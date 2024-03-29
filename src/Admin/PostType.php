<?php

/**
 * Handles Post Type
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Store;
use BracketSpace\Notification\Dependencies\Micropackage\Ajax\Response;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;
use function BracketSpace\Notification\adaptNotificationFrom;
use function BracketSpace\Notification\addNotification;

/**
 * PostType class
 */
class PostType
{
	/**
	 * TABLE OF CONTENTS: -------------------------------
	 * - Post Type.
	 * - Delete.
	 * - Save.
	 * - AJAX.
	 * - Notifications.
	 * --------------------------------------------------
	 */

	/**
	 * --------------------------------------------------
	 * Post Type.
	 * --------------------------------------------------
	 */

	/**
	 * Registers Notification post type
	 *
	 * @action init
	 *
	 * @return void
	 */
	public function register()
	{
		$labels = [
			'name' => __('Notifications', 'notification'),
			'singular_name' => __('Notification', 'notification'),
			'add_new' => _x(
				'Add New Notification',
				'notification',
				'notification'
			),
			'add_new_item' => __('Add New Notification', 'notification'),
			'edit_item' => __('Edit Notification', 'notification'),
			'new_item' => __('New Notification', 'notification'),
			'view_item' => __('View Notification', 'notification'),
			'search_items' => __('Search Notifications', 'notification'),
			'not_found' => __('No Notifications found', 'notification'),
			'not_found_in_trash' => __('No Notifications found in Trash', 'notification'),
			'parent_item_colon' => __('Parent Notification:', 'notification'),
			'menu_name' => __('Notifications', 'notification'),
		];

		register_post_type(
			'notification',
			[
				'labels' => apply_filters('notification/whitelabel/cpt/labels', $labels),
				'hierarchical' => false,
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => apply_filters('notification/whitelabel/cpt/parent', true),
				'show_in_admin_bar' => true,
				'menu_icon' => \Notification::fs()->image_to_base64('resources/images/menu-icon.svg'),
				'menu_position' => 103,
				'show_in_nav_menus' => false,
				'publicly_queryable' => false,
				'exclude_from_search' => true,
				'has_archive' => false,
				'query_var' => false,
				'can_export' => true,
				'rewrite' => false,
				'capabilities' => apply_filters(
					'notification/post_type/capabilities',
					[
						'edit_post' => 'manage_options',
						'read_post' => 'manage_options',
						'delete_post' => 'manage_options',
						'edit_posts' => 'manage_options',
						'edit_others_posts' => 'manage_options',
						'delete_posts' => 'manage_options',
						'publish_posts' => 'manage_options',
						'read_private_posts' => 'manage_options',
					]
				),
				'supports' => ['title'],
			]
		);
	}

	/**
	 * Filters the post updated messages
	 *
	 * @filter post_updated_messages
	 *
	 * @param array<mixed> $messages Messages.
	 * @return array<mixed>
	 * @since  5.2.0
	 */
	public function postUpdatedMessages($messages)
	{
		$messages['notification'] = [
			'',
			__('Notification updated.', 'notification'),
			'',
			'',
			__('Notification updated.', 'notification'),
			'',
			__('Notification saved.', 'notification'),
			__('Notification saved.', 'notification'),
			'',
			'',
			'',
		];

		return $messages;
	}

	/**
	 * Filters the bulk action messages
	 *
	 * @filter bulk_post_updated_messages
	 *
	 * @param array<mixed> $bulkMessages Messages.
	 * @param array<mixed> $bulkCounts Counters.
	 * @return array<mixed>
	 * @since  6.0.0
	 */
	public function bulkActionMessages($bulkMessages, $bulkCounts)
	{
		$bulkMessages['notification'] = [
			// translators: Number of Notifications.
			'deleted' => _n(
				'%s notification removed.',
				'%s notifications removed.',
				$bulkCounts['trashed']
			),
		];

		return $bulkMessages;
	}

	/**
	 * Changes the notification post statuses above the post table
	 *
	 * @filter views_edit-notification
	 *
	 * @param array<mixed> $statuses Statuses array.
	 * @return array<mixed>
	 * @since  6.0.0
	 */
	public function changePostStatuses($statuses)
	{
		if (isset($statuses['publish'])) {
			$statuses['publish'] = str_replace(
				__('Published', 'notification'),
				__('Active', 'notification'),
				$statuses['publish']
			);
		}

		if (isset($statuses['draft'])) {
			$statuses['draft'] = str_replace(
				__('Draft', 'notification'),
				__('Disabled', 'notification'),
				$statuses['draft']
			);
		}

		return $statuses;
	}

	/**
	 * --------------------------------------------------
	 * Delete.
	 * --------------------------------------------------
	 */

	/**
	 * Deletes the post entirely bypassing the trash
	 *
	 * @action wp_trash_post 100
	 *
	 * @param int $postId Post ID.
	 * @return void
	 * @since  6.0.0
	 */
	public function bypassTrash($postId)
	{
		if (get_post_type($postId) !== 'notification') {
			return;
		}

		wp_delete_post($postId, true);
	}

	/**
	 * --------------------------------------------------
	 * Save.
	 * --------------------------------------------------
	 */

	/**
	 * Creates Notification unique hash
	 *
	 * @filter wp_insert_post_data 100
	 *
	 * @param array<mixed> $data post data.
	 * @param array<mixed> $postarr saved data.
	 * @return array<mixed>
	 * @since  6.0.0
	 */
	public function createNotificationHash($data, $postarr)
	{
		// Another save process is in progress, abort.
		if (defined('DOING_NOTIFICATION_SAVE') && DOING_NOTIFICATION_SAVE) {
			return $data;
		}

		if ($data['post_type'] !== 'notification') {
			return $data;
		}

		if (!preg_match('/notification_[a-z0-9]{13}/', $data['post_name'])) {
			$data['post_name'] = Notification::createHash();
		}

		return $data;
	}

	/**
	 * Saves the Notification data
	 *
	 * @action save_post_notification
	 *
	 * @param int $postId Current post ID.
	 * @param object $post WP_Post object.
	 * @param bool $update If existing notification is updated.
	 * @return void
	 */
	public function save($postId, $post, $update)
	{
		// Another save process is in progress, abort.
		if (defined('DOING_NOTIFICATION_SAVE') && DOING_NOTIFICATION_SAVE) {
			return;
		}

		if (
			!isset($_POST['notification_data_nonce']) ||
			!wp_verify_nonce(
				sanitize_text_field(wp_unslash($_POST['notification_data_nonce'])),
				'notification_post_data_save'
			)
		) {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		if (!$update) {
			return;
		}

		// Prevent infinite loops.
		if (!defined('DOING_NOTIFICATION_SAVE')) {
			// phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
			define('DOING_NOTIFICATION_SAVE', true);
		}

		$data = $_POST;
		$notificationPost = adaptNotificationFrom('WordPress', $post);

		// Title.
		if (isset($data['post_title'])) {
			$notificationPost->setTitle($data['post_title']);
		}

		// Status.
		$status = (isset($data['notification_onoff_switch']) && $data['notification_onoff_switch'] === '1');
		$notificationPost->setEnabled($status);

		// Trigger.
		if (!empty($data['notification_trigger'])) {
			$trigger = Store\Trigger::get($data['notification_trigger']);
			if (!empty($trigger)) {
				$notificationPost->setTrigger($trigger);
			}
		}

		// Prepare Carriers to save.
		$carriers = [];

		foreach (Store\Carrier::all() as $carrier) {
			if (!isset($data['notification_carrier_' . $carrier->getSlug()])) {
				continue;
			}

			$carrierData = $data['notification_carrier_' . $carrier->getSlug()];

			if (!$carrierData['activated']) {
				continue;
			}

			// If nonce not set or false, ignore this form.
			if (!wp_verify_nonce($carrierData['_nonce'], $carrier->getSlug() . '_carrier_security')) {
				continue;
			}

			// @todo #h1kf7 `enabled` key is overwritten below.
			$carrier->setData($carrierData);

			if (isset($data['notification_carrier_' . $carrier->getSlug() . '_enable'])) {
				$carrier->enable();
			} else {
				$carrier->disable();
			}

			$carriers[$carrier->getSlug()] = $carrier;
		}

		$notificationPost->setCarriers($carriers);

		// Hook into this action if you want to save any Notification Post data.
		do_action('notification/data/save', $notificationPost);

		$notificationPost->save();

		/**
		 * @todo
		 * This cache should be cleared in Adapter save method.
		 * Now it's used in Admin\Wizard::addNotifications() as well
		 */
		$cache = new CacheDriver\ObjectCache('notification');
		$cache->set_key('notifications');
		$cache->delete();

		do_action('notification/data/save/after', $notificationPost);
	}

	/**
	 * --------------------------------------------------
	 * AJAX.
	 * --------------------------------------------------
	 */

	/**
	 * Changes notification status from AJAX call
	 *
	 * @action wp_ajax_change_notification_status
	 *
	 * @return void
	 */
	public function ajaxChangeNotificationStatus()
	{
		check_ajax_referer('notification_csrf');

		$ajax = new Response();
		$data = $_POST;
		$error = false;

		$ajax->verify_nonce('change_notification_status_' . $data['post_id']);

		$adapter = adaptNotificationFrom('WordPress', (int)$data['post_id']);
		$adapter->setEnabled($data['status'] === 'true');

		$result = $adapter->save();

		if (is_wp_error($result)) {
			$ajax->error(
				__("Notification status couldn't be changed.", 'notification')
			);
		}

		$ajax->send(true);
	}

	/**
	 * --------------------------------------------------
	 * Notifications.
	 * --------------------------------------------------
	 */

	/**
	 * Gets all Notifications from database.
	 * Uses direct database call for performance.
	 *
	 * @return array<mixed>
	 * @since  6.0.0
	 */
	public static function getAllNotifications()
	{
		$driver = new CacheDriver\ObjectCache('notification');
		$cache = new Cache($driver, 'notifications');

		return $cache->collect(
			static function () {
				global $wpdb;

				$sql = "SELECT p.post_content
				FROM {$wpdb->posts} p
				WHERE p.post_type = 'notification' AND p.post_status = 'publish'
				ORDER BY p.menu_order ASC, p.post_modified DESC";

				// We're using direct db call for performance purposes - we only need the post_content field.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
				return $wpdb->get_col($sql);
			}
		);
	}

	/**
	 * Sets up all the Notification from database
	 * It's running on every single page load.
	 *
	 * @action notification/init 9999999
	 *
	 * @return void
	 * @since  6.0.0
	 */
	public function setupNotifications()
	{
		$notifications = self::getAllNotifications();

		foreach ($notifications as $notificationJson) {
			if (empty($notificationJson)) {
				continue;
			}

			// Check if Notification has valid JSON.
			$jsonCheck = json_decode(
				$notificationJson,
				true
			);
			if (json_last_error() !== JSON_ERROR_NONE) {
				continue;
			}

			$adapter = adaptNotificationFrom('JSON', $notificationJson);

			// Set source back to WordPress.
			$adapter->setSource('WordPress');

			// Check if the notification hasn't been added already ie. via Sync.
			if (Store\Notification::has($adapter->getHash())) {
				continue;
			}

			addNotification($adapter->getNotification());
		}
	}
}
