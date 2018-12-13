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
	 * @param PostData     $postdata     PostData class.
	 */
	public function __construct( BoxRenderer $boxrenderer, FormRenderer $formrenderer, PostData $postdata ) {
		$this->boxrenderer  = $boxrenderer;
		$this->formrenderer = $formrenderer;
		$this->postdata     = $postdata;
	}

	/**
	 * Renders notification boxes
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function render_notifications() {

		foreach ( notification_get_notifications() as $notification ) {

			$this->postdata->set_notification_data( $notification );

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

		$this->postdata->set_post_id( $post_id );
		$this->postdata->save_notification_data( $_POST );

		do_action( 'notification/data/save', $this->postdata, $post_id );

	}

}
