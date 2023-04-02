<?php

/**
 * Notification duplicator class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

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
			__(
				'Duplicate',
				'notification'
			)
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
		check_admin_referer(
			'duplicate_notification',
			'nonce'
		);

		if (!isset($_GET['duplicate'])) {
			exit;
		}

		// Get the source notification post.
		$source = get_post(intval(wp_unslash($_GET['duplicate'])));
		$wp = notificationAdaptFrom(
			'WordPress',
			$source
		);

		/**
		 * JSON Adapter
		 *
		 * @var \BracketSpace\Notification\Defaults\Adapter\JSON
		 */
		$json = notificationSwapAdapter(
			'JSON',
			$wp
		);

		$json->refreshHash();
		$json->setEnabled(false);

		if (get_post_type($source) !== 'notification') {
			wp_die('You cannot duplicate post that\'s not Notification post');
		}

		$newId = wp_insert_post(
			[
				'post_title' => sprintf(
					'(%s) %s',
					__(
						'Duplicate',
						'notification'
					),
					// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
					$source->post_title
				),
				'post_content' => wp_slash($json->save(JSON_UNESCAPED_UNICODE)),
				'post_status' => 'draft',
				'post_type' => 'notification',
			]
		);

		wp_safe_redirect(html_entity_decode(get_edit_post_link($newId)));
		exit;
	}
}
