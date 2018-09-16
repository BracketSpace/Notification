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
		$columns['switch']        = __( 'Status', 'notification' );
		$columns['title']         = $title_column;
		$columns['trigger']       = __( 'Trigger', 'notification' );
		$columns['notifications'] = __( 'Notifications', 'notification' );
		$columns['date']          = $date_column;

		return $columns;

	}

	/**
	 * Content for custom columns
	 *
	 * @action manage_notification_posts_custom_column
	 *
	 * @param  string  $column  column slug.
	 * @param  integer $post_id post ID.
	 * @return void
	 */
	public function table_column_content( $column, $post_id ) {

		switch ( $column ) {
			case 'trigger':
				$trigger_slug = get_post_meta( $post_id, '_trigger', true );
				$trigger      = notification_get_single_trigger( $trigger_slug );

				if ( false === $trigger ) {
					esc_html_e( 'No trigger selected', 'notification' );
				} else {
					echo esc_html( $trigger->get_name() );
				}
				break;

			case 'switch':
				$checked = 'draft' === get_post_status( $post_id ) ? '0' : '1';

				echo '<div class="onoffswitch" data-postid="' . esc_attr( $post_id ) . '" data-nonce="' . wp_create_nonce( 'change_notification_status_' . $post_id ) . '">'; // WPCS: XSS ok.
					echo '<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" value="1" id="onoffswitch-' . esc_attr( $post_id ) . '" ' . checked( $checked, '1', false ) . '>';
					echo '<label class="onoffswitch-label" for="onoffswitch-' . esc_attr( $post_id ) . '">';
						echo '<span class="onoffswitch-inner"></span>';
						echo '<span class="onoffswitch-switch"></span>';
					echo '</label>';
				echo '</div>';
				break;

			case 'notifications':
				$enabled_notifications = (array) get_post_meta( $post_id, '_enabled_notification', false );

				foreach ( array_unique( $enabled_notifications ) as $notification_slug ) {
					$notification = notification_get_single_notification( $notification_slug );
					if ( ! empty( $notification ) ) {
						echo esc_html( $notification->get_name() );
						echo '<br>';
					}
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
			return array();
		}

		return $post_states;

	}

	/**
	 * Remove quick edit from post inline actions
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

}
