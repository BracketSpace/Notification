<?php
/**
 * Handles Notification post table
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

/**
 * PostTable class
 */
class PostTable {

	/**
	 * Adds custom table columns
	 *
	 * @filter manage_notification_posts_columns
	 *
	 * @param  array $columns current columns.
	 * @return array          filtered columns
	 */
	public function table_columns( $columns ) {

		$date_column  = $columns['date'];
		$title_column = $columns['title'];
		unset( $columns['date'] );
		unset( $columns['title'] );

		// Custom columns.
		$columns['switch']   = __( 'Status', 'notification' );
		$columns['title']    = $title_column;
		$columns['trigger']  = __( 'Trigger', 'notification' );
		$columns['carriers'] = __( 'Carriers', 'notification' );
		$columns['date']     = $date_column;

		return $columns;

	}

	/**
	 * Content for custom columns
	 *
	 * @action manage_notification_posts_custom_column
	 *
	 * @param  string  $column  Column slug.
	 * @param  integer $post_id Post ID.
	 * @return void
	 */
	public function table_column_content( $column, $post_id ) {

		$notification = notification_adapt_from( 'WordPress', $post_id );

		switch ( $column ) {
			case 'trigger':
				$trigger = $notification->get_trigger();
				echo $trigger ? esc_html( $trigger->get_name() ) : esc_html__( 'No trigger selected', 'notification' );
				break;

			case 'switch':
				echo '<div class="onoffswitch" data-postid="' . esc_attr( $post_id ) . '" data-nonce="' . esc_attr( wp_create_nonce( 'change_notification_status_' . $post_id ) ) . '">';
					echo '<input type="checkbox" name="notification_onoff_switch" class="onoffswitch-checkbox" value="1" id="onoffswitch-' . esc_attr( $post_id ) . '" ' . checked( $notification->is_enabled(), true, false ) . '>';
					echo '<label class="onoffswitch-label" for="onoffswitch-' . esc_attr( $post_id ) . '">';
						echo '<span class="onoffswitch-inner"></span>';
						echo '<span class="onoffswitch-switch"></span>';
					echo '</label>';
				echo '</div>';
				break;

			case 'carriers':
				foreach ( $notification->get_enabled_carriers() as $carrier ) {
					echo esc_html( $carrier->get_name() );
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
	 * @param array   $post_states an array of post display states.
	 * @param WP_Post $post        the current post object.
	 * @return array               filtered states
	 */
	public function remove_status_display( $post_states, $post ) {

		if ( 'notification' === $post->post_type ) {
			return [];
		}

		return $post_states;

	}

	/**
	 * Removes quick edit from post inline actions
	 *
	 * @filter post_row_actions
	 *
	 * @param  array  $row_actions array with action links.
	 * @param  object $post        WP_Post object.
	 * @return array               filtered actions
	 */
	public function remove_quick_edit( $row_actions, $post ) {

		if ( 'notification' === $post->post_type ) {
			if ( isset( $row_actions['inline hide-if-no-js'] ) ) {
				unset( $row_actions['inline hide-if-no-js'] );
			}
			if ( isset( $row_actions['inline'] ) ) {
				unset( $row_actions['inline'] );
			}
		}

		return $row_actions;

	}

	/**
	 * Changes trash link to something more descriptive
	 * Notifications cannot be trashed, it can be only removed
	 *
	 * @filter post_row_actions
	 *
	 * @param  array  $row_actions array with action links.
	 * @param  object $post        WP_Post object.
	 * @return array               filtered actions
	 */
	public function adjust_trash_link( $row_actions, $post ) {

		if ( 'notification' !== $post->post_type ) {
			return $row_actions;
		}

		$row_actions['trash'] = '<a href="' . esc_url( get_delete_post_link( $post->ID, '', true ) ) . '" class="submitdelete notification-delete-post">' . esc_html__( 'Remove', 'notification' ) . '</a>';

		return $row_actions;

	}

	/**
	 * Changes the table bulk actions.
	 *
	 * @filter bulk_actions-edit-notification
	 *
	 * @param  array $actions Bulk actions array.
	 * @return array          Filtered actions
	 */
	public function adjust_bulk_actions( $actions ) {

		unset( $actions['edit'] );
		unset( $actions['trash'] );

		$actions['delete']  = __( 'Remove', 'notification' );
		$actions['disable'] = __( 'Disable', 'notification' );
		$actions['enable']  = __( 'Enable', 'notification' );

		return $actions;

	}

	/**
	 * Handles disable notification bulk actions
	 *
	 * @filter handle_bulk_actions-edit-notification 10
	 *
	 * @since [Next]
	 * @param  string $redirect_to Redirect to link.
	 * @param  string $doaction Action to perform.
	 * @param  array  $post_ids Array with post ids.
	 *
	 * @return string Redirect link.
	 */
	public function handle_disable_bulk_actions( $redirect_to, $doaction, $post_ids ) {
		if ( 'disable' !== $doaction && 'enable' !== $doaction ) {
			return $redirect_to;
		}

		$redirect_to = remove_query_arg( array( 'bulk_disable_notifications', 'bulk_enable_notifications' ), $redirect_to );

		$notification_status = true;

		if ( 'disable' === $doaction ) {
			$notification_status = false;
		}

		foreach ( $post_ids as $post_id ) {
			$notification = notification_adapt_from( 'WordPress', $post_id );
			$notification->set_enabled( $notification_status );
			$notification->save();
		}

		$redirect_to = add_query_arg( 'bulk_' . $doaction . '_notifications', count( $post_ids ), $redirect_to );

		return $redirect_to;
	}

	/**
	 * Notification enable/disable admin notices
	 *
	 * @action admin_notices
	 * @since [Next]
	 */
	public function display_bulk_actions_admin_notices() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_REQUEST['bulk_disable_notifications'] || ! empty( $_REQUEST['bulk_enable_notifications'] ) ) ) {
			$action_type = '';
			$bulk_count  = 0;

			if ( ! empty( $_REQUEST['bulk_disable_notifications'] ) ) {
				$action_type = esc_html__( 'disabled', 'notification' );
				$bulk_count  = intval( $_REQUEST['bulk_disable_notifications'] );

			} else {
				$action_type = esc_html__( 'enabled', 'notification' );
				$bulk_count  = intval( $_REQUEST['bulk_enable_notifications'] );
			}
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			// translators: Number of Notifications.
			printf( '<div id="message" class="updated notice is-dismissible"><p>' . _n( '%1$s notification %2$s.', '%1$s notifications %2$s.', $bulk_count, $action_type ) . '</p></div>', $bulk_count, $action_type ); // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

}
