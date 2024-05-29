<?php

/**
 * Handles Post Type
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Database\NotificationDatabaseService as Db;
use BracketSpace\Notification\Store;
use BracketSpace\Notification\Dependencies\Micropackage\Ajax\Response;

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
	 * And removes the Notification from custom table
	 *
	 * @action wp_trash_post 100
	 *
	 * @since 6.0.0
	 * @param int $postId Post ID.
	 * @return void
	 */
	public function bypassTrash($postId)
	{
		if (get_post_type($postId) !== 'notification') {
			return;
		}

		// Another delete process is in progress, abort.
		if (Db::doingOperation() !== false) {
			return;
		}

		wp_delete_post($postId, true);
	}

	/**
	 * Removes the Notification from custom table upon WP Post deletion
	 *
	 * @action after_delete_post 100
	 *
	 * @since [Next]
	 * @param int $postId Post ID.
	 * @return void
	 */
	public function deleteNotification($postId)
	{
		// Another delete process is in progress, abort.
		if (Db::doingOperation() !== false) {
			return;
		}

		$notification = Db::postToNotification($postId);

		if ($notification === null) {
			return;
		}

		Db::delete($notification->getHash());
	}

	/**
	 * --------------------------------------------------
	 * Save.
	 * --------------------------------------------------
	 */

	/**
	 * Saves the Notification data
	 *
	 * @action save_post_notification
	 *
	 * @since [Next] We're saving the Notification to custom table instead of Post Type. Post is just the shell.
	 * @param int $postId Current post ID.
	 * @param \WP_Post $post WP_Post object.
	 * @param bool $update If existing notification is updated.
	 * @return void
	 */
	public function save($postId, $post, $update)
	{
		// Another save process is in progress, abort.
		if (Db::doingOperation() !== false) {
			return;
		}

		if (
			! isset($_POST['notification_data_nonce']) ||
			! wp_verify_nonce(
				sanitize_text_field(wp_unslash($_POST['notification_data_nonce'])),
				'notification_post_data_save'
			)
		) {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		if (! $update) {
			return;
		}

		$data = $_POST;

		$notification = Db::postToNotification($post) ?? new Notification();

		// Hash.
		if (isset($data['post_name'])) {
			$hash = empty($data['post_name']) ? Notification::createHash() : $data['post_name'];
			$notification->setHash($hash);
		}

		// Title.
		if (isset($data['post_title'])) {
			$notification->setTitle($data['post_title']);
		}

		// Status.
		$status = (isset($data['notification_onoff_switch']) && $data['notification_onoff_switch'] === '1');
		$notification->setEnabled($status);

		// Trigger.
		if (!empty($data['notification_trigger'])) {
			$trigger = Store\Trigger::get($data['notification_trigger']);
			if (! empty($trigger)) {
				$notification->setTrigger($trigger);
			}
		}

		// Prepare Carriers to save.
		$carriers = [];

		foreach (Store\Carrier::all() as $carrier) {
			if (! isset($data['notification_carrier_' . $carrier->getSlug()])) {
				continue;
			}

			$carrierData = $data['notification_carrier_' . $carrier->getSlug()];

			if (! $carrierData['activated']) {
				continue;
			}

			// If nonce not set or false, ignore this form.
			if (! wp_verify_nonce($carrierData['_nonce'], $carrier->getSlug() . '_carrier_security')) {
				continue;
			}

			$carrier->setData($carrierData);

			if (isset($data['notification_carrier_' . $carrier->getSlug() . '_enable'])) {
				$carrier->enable();
			} else {
				$carrier->disable();
			}

			$carriers[$carrier->getSlug()] = $carrier;
		}

		$notification->setCarriers($carriers);

		Db::upsert($notification);
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
		$errorMessage = __("Notification status couldn't be changed.", 'notification');

		$ajax->verify_nonce('change_notification_status_' . $data['post_id']);

		$notification = Db::postToNotification($data['post_id']);

		if ($notification === null) {
			$ajax->error($errorMessage);
		} else {
			$notification->setEnabled($data['status'] === 'true');

			Db::upsert($notification);
		}

		$ajax->send(true);
	}

	/**
	 * Gets all Notifications from database.
	 *
	 * @deprecated [Next] Use BracketSpace\Notification\Database\NotificationDatabaseService::getAll();
	 * @since  6.0.0
	 * @return array<Notification>
	 */
	public static function getAllNotifications()
	{
		_deprecated_function(
			__METHOD__,
			'[Next]',
			'BracketSpace\Notification\Database\NotificationDatabaseService::getAll'
		);

		return Db::getAll();
	}
}
