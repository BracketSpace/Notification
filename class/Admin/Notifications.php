<?php
/**
 * Handles Notifications metaboxes
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

/**
 * Notifications class
 */
class Notifications {

	/**
	 * Notifications constructor
	 *
	 * @since 5.0.0
	 * @param BoxRenderer  $boxrenderer  BoxRenderer class.
	 * @param FormRenderer $formrenderer FormRenderer class.
	 */
	public function __construct( BoxRenderer $boxrenderer, FormRenderer $formrenderer ) {
		$this->boxrenderer  = $boxrenderer;
		$this->formrenderer = $formrenderer;
	}

	/**
	 * Renders notification boxes
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function render_notifications() {

		foreach ( notification_get_notifications() as $notification ) {

			notification_populate_notification( $notification );

			$this->formrenderer->set_fields( $notification->get_form_fields() );

			$this->boxrenderer->set_vars(
				array(
					'id'      => 'notification_type_' . $notification->get_slug(),
					'name'    => 'notification_' . $notification->get_slug() . '_enable',
					'title'   => $notification->get_name(),
					'content' => $this->formrenderer->render(),
					'open'    => $notification->enabled,
				)
			);

			$this->boxrenderer->render();

		}

	}

	/**
	 * Save the notifications in post meta
	 *
	 * @action save_post_notification
	 *
	 * @param  integer $post_id current post ID.
	 * @param  object  $post    WP_Post object.
	 * @param  boolean $update  if existing notification is updated.
	 * @return void
	 */
	public function save( $post_id, $post, $update ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! $update ) {
			return;
		}

		// Bail if we are saving just the status.
		if ( isset( $_POST['action'] ) && 'change_notification_status' === $_POST['action'] ) {
			return;
		}

		$data              = $_POST;
		$notification_post = notification_get_post( $post );

		// Enable all notifications one by one.
		foreach ( notification_get_notifications() as $notification ) {
			if ( isset( $data[ 'notification_' . $notification->get_slug() . '_enable' ] ) ) {
				$notification_post->enable_notification( $notification->get_slug() );
			} else {
				$notification_post->disable_notification( $notification->get_slug() );
			}
		}

		// Save all notification settings one by one.
		foreach ( notification_get_notifications() as $notification ) {

			if ( ! isset( $data[ 'notification_type_' . $notification->get_slug() ] ) ) {
				continue;
			}

			$ndata = $data[ 'notification_type_' . $notification->get_slug() ];

			// nonce not set or false, ignoring this form.
			if ( ! wp_verify_nonce( $ndata['_nonce'], $notification->get_slug() . '_notification_security' ) ) {
				continue;
			}

			$notification_post->set_notification_data( $notification->get_slug(), $ndata );

			do_action( 'notification/notification/saved', $notification_post->get_id(), $notification, $ndata );

		}

		do_action( 'notification/data/save', $notification_post );

	}

}
