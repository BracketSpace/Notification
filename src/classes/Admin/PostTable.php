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

}
