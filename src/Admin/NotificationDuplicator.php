<?php

/**
 * Notification duplicator class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Database\NotificationDatabaseService as Db;

/**
 * Notification duplicator class
 */
class NotificationDuplicator
{
	/**
	 * Adds duplicate link to row actions
	 *
	 * @filter post_row_actions 50
	 *
	 * @param array<mixed> $rowActions array with action links.
	 * @param object $post WP_Post object.
	 * @return array<mixed>               filtered actions
	 */
	public function addDuplicateRowAction($rowActions, $post)
	{
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($post->post_type !== 'notification') {
			return $rowActions;
		}

		$rowActions['duplicate'] = sprintf(
			'<a href="%s">%s</a>',
			admin_url(
				sprintf(
					'admin-post.php?action=notification_duplicate&duplicate=%s&nonce=%s',
					$post->ID,
					wp_create_nonce('duplicate_notification')
				)
			),
			__('Duplicate', 'notification')
		);

		return $rowActions;
	}

	/**
	 * Duplicates the notification
	 *
	 * @action admin_post_notification_duplicate
	 *
	 * @return void
	 * @since  5.2.3
	 */
	public function notificationDuplicate()
	{
		check_admin_referer('duplicate_notification', 'nonce');

		if (! isset($_GET['duplicate'])) {
			exit;
		}

		// Get the source notification post.
		$source = get_post(intval(wp_unslash($_GET['duplicate'])));

		if (get_post_type($source) !== 'notification' || ! $source instanceof \WP_Post) {
			wp_die("You cannot duplicate post that's not a Notification post");
		}

		$notification = Db::postToNotification($source);

		if ($notification === null) {
			wp_die("It doesn't seem that Notification exist anymore");
		}

		$newNotification = clone $notification;
		$newNotification->refreshHash();
		$newNotification->setEnabled(false);
		$newNotification->setTitle(sprintf('%s — duplicate', $notification->getTitle()));

		// Create duplicated Notification.
		Db::upsert($newNotification);

		wp_safe_redirect(html_entity_decode(get_edit_post_link(Db::getLastUpsertedPostId())));
		exit;
	}
}
