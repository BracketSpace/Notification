<?php

/**
 * Handles Notification post table
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

/**
 * PostTable class
 */
class PostTable
{
	/**
	 * Adds custom table columns
	 *
	 * @filter manage_notification_posts_columns
	 *
	 * @param array<mixed> $columns current columns.
	 * @return array<mixed> filtered columns
	 */
	public function tableColumns($columns)
	{
		$dateColumn = $columns['date'];
		$titleColumn = $columns['title'];
		unset($columns['date']);
		unset($columns['title']);

		// Custom columns.
		$columns['switch'] = __('Status', 'notification');
		$columns['title'] = $titleColumn;
		$columns['hash'] = __('Hash', 'notification');
		$columns['trigger'] = __('Trigger', 'notification');
		$columns['carriers'] = __('Carriers', 'notification');
		$columns['date'] = $dateColumn;

		return $columns;
	}

	/**
	 * Content for custom columns
	 *
	 * @action manage_notification_posts_custom_column
	 *
	 * @param string $column Column slug.
	 * @param int $postId Post ID.
	 * @return void
	 */
	public function tableColumnContent($column, $postId)
	{
		/**
		 * WordPress Adapter
		 *
		 * @var \BracketSpace\Notification\Defaults\Adapter\WordPress
		 */
		$notification = notificationAdaptFrom(
			'WordPress',
			$postId
		);

		switch ($column) {
			case 'hash':
				echo '<code>' . esc_html($notification->getHash()) . '</code>';
				break;

			case 'trigger':
				$trigger = $notification->getTrigger();
				echo esc_html(
					$trigger === null
						? __('No trigger selected', 'notification')
						: $trigger->getName()
				);
				break;

			case 'switch':
				echo '<div class="onoffswitch" data-postid="' . esc_attr(
					(string)$postId
				) . '" data-nonce="' . esc_attr(wp_create_nonce('change_notification_status_' . $postId)) . '">';
				echo '<input
					type="checkbox"
					name="notification_onoff_switch"
					class="onoffswitch-checkbox"
					value="1"
					id="onoffswitch-' . esc_attr(
					(string)$postId
				) . '" ' . checked(
					$notification->isEnabled(),
					true,
					false
				) . '>';
				echo '<label class="onoffswitch-label" for="onoffswitch-' . esc_attr((string)$postId) . '">';
				echo '<span class="onoffswitch-inner"></span>';
				echo '<span class="onoffswitch-switch"></span>';
				echo '</label>';
				echo '</div>';
				break;

			case 'carriers':
				foreach ($notification->getEnabledCarriers() as $carrier) {
					echo esc_html($carrier->getName());
					echo '<br>';
				}
				break;
		}
	}

	/**
	 * Remove all inline states to be displayed on notifications table
	 *
	 * @filter display_post_states
	 *
	 * @param array<mixed> $postStates an array of post display states.
	 * @param \WP_Post $post the current post object.
	 * @return array<mixed>               filtered states
	 */
	public function removeStatusDisplay($postStates, $post)
	{
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($post->post_type === 'notification') {
			return [];
		}

		return $postStates;
	}

	/**
	 * Removes quick edit from post inline actions
	 *
	 * @filter post_row_actions
	 *
	 * @param array<mixed> $rowActions array with action links.
	 * @param object $post WP_Post object.
	 * @return array<mixed> filtered actions
	 */
	public function removeQuickEdit($rowActions, $post)
	{
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($post->post_type === 'notification') {
			if (isset($rowActions['inline hide-if-no-js'])) {
				unset($rowActions['inline hide-if-no-js']);
			}
			if (isset($rowActions['inline'])) {
				unset($rowActions['inline']);
			}
		}

		return $rowActions;
	}

	/**
	 * Changes trash link to something more descriptive
	 * Notifications cannot be trashed, it can be only removed
	 *
	 * @filter post_row_actions
	 *
	 * @param array<mixed> $rowActions array with action links.
	 * @param object $post WP_Post object.
	 * @return array<mixed>               filtered actions
	 */
	public function adjustTrashLink($rowActions, $post)
	{
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		if ($post->post_type !== 'notification') {
			return $rowActions;
		}

		$rowActions['trash'] = '<a href="' .
			esc_url(get_delete_post_link($post->ID, '', true))
			. '" class="submitdelete notification-delete-post">'
			. esc_html__('Remove', 'notification') . '</a>';

		return $rowActions;
	}

	/**
	 * Changes the table bulk actions.
	 *
	 * @filter bulk_actions-edit-notification
	 *
	 * @param array<mixed> $actions Bulk actions array.
	 * @return array<mixed>          Filtered actions
	 */
	public function adjustBulkActions($actions)
	{
		unset($actions['edit']);
		unset($actions['trash']);

		$actions['delete'] = __('Remove', 'notification');
		$actions['disable'] = __('Disable', 'notification');
		$actions['enable'] = __('Enable', 'notification');

		return $actions;
	}

	/**
	 * Handles status bulk actions
	 *
	 * @filter handle_bulk_actions-edit-notification 10
	 *
	 * @param string $redirectTo Redirect to link.
	 * @param string $doaction Action to perform.
	 * @param array<mixed> $postIds Array with post ids.
	 * @return string              Redirect link.
	 * @since  7.1.0
	 */
	public function handleStatusBulkActions($redirectTo, $doaction, $postIds)
	{
		if (
			!in_array(
				$doaction,
				['enable', 'disable'],
				true
			)
		) {
			return $redirectTo;
		}

		$redirectTo = remove_query_arg(
			['bulk_disable_notifications', 'bulk_enable_notifications'],
			$redirectTo
		);

		foreach ($postIds as $postId) {
			$notification = notificationAdaptFrom(
				'WordPress',
				$postId
			);
			$notification->setEnabled($doaction === 'enable');
			$notification->save();
		}

		$action = sprintf('bulk_%s_notifications', $doaction);

		return add_query_arg(
			[
				$action => count($postIds),
				'nonce' => wp_create_nonce('notification_bulk_action'),
			],
			$redirectTo
		);
	}

	/**
	 * Prints notices for bulk status changes.
	 *
	 * @action admin_notices
	 *
	 * @return void
	 * @since 7.1.0
	 */
	public function displayBulkActionsAdminNotices()
	{
		if (!isset($_GET['bulk_disable_notifications'], $_GET['bulk_enable_notifications'])) {
			return;
		}

		check_admin_referer(
			'notification_bulk_action',
			'nonce'
		);

		$action = $_GET;

		if (!empty($action['bulk_disable_notifications'])) {
			$actionType = esc_html__('disabled', 'notification');
			$bulkCount = intval($action['bulk_disable_notifications']);
		} else {
			$actionType = esc_html__('enabled', 'notification');
			$bulkCount = intval($action['bulk_enable_notifications']);
		}

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			// translators: 1. Number of Notifications, 2. Action taken disabled|enabled.
			'<div id="message" class="updated notice is-dismissible"><p>' . _n(
				'%1$s notification %2$s.',
				'%1$s notifications %2$s.',
				$bulkCount,
				$actionType
			) . '</p></div>',
			$bulkCount,
			$actionType
		);
	}
}
