<?php
/**
 * Handles Trigger metabox
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\View;

/**
 * Trigger class
 */
class Trigger {

	/**
	 * MergeTags constructor
	 *
	 * @since 5.0.0
	 * @param View $view View class.
	 */
	public function __construct( View $view ) {
		$this->view = $view;
	}

	/**
	 * Prints trigger select
	 *
	 * @param  object $post current WP_Post.
	 * @return void
	 */
	public function render_select( $post ) {

		$grouped_triggers = notification_get_triggers_grouped();
		$notification     = notification_get_post( $post );

		$this->view->set_var( 'post', $post );
		$this->view->set_var( 'selected', $notification->get_trigger() );
		$this->view->set_var( 'triggers', $grouped_triggers );
		$this->view->set_var( 'has_triggers', ! empty( $grouped_triggers ) );
		$this->view->set_var( 'select_name', 'notification_trigger' );

		$this->view->get_view( 'trigger/metabox' );

	}

	/**
	 * Save the trigger in post meta (key: _trigger)
	 *
	 * @action save_post_notification
	 *
	 * @param  integer $post_id current post ID.
	 * @param  object  $post    WP_Post object.
	 * @param  boolean $update  if existing notification is updated.
	 * @return void
	 */
	public function save( $post_id, $post, $update ) {

		if ( ! isset( $_POST['trigger_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['trigger_nonce'] ) ), 'notification_trigger' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! $update ) {
			return;
		}

		$notification_post    = notification_get_post( $post );
		$notification_trigger = ! empty( $_POST['notification_trigger'] ) ? sanitize_text_field( wp_unslash( $_POST['notification_trigger'] ) ) : '';

		$notification_post->set_trigger( $notification_trigger );

	}

}
