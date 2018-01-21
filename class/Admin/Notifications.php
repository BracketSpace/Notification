<?php
/**
 * Handles Notifications metaboxes
 */

namespace underDEV\Notification\Admin;
use underDEV\Notification\Notifications as NotificationsSet;

class Notifications {

	public function __construct( NotificationsSet $notifications, BoxRenderer $boxrenderer, FormRenderer $formrenderer, PostData $postdata ) {
		$this->notifications = $notifications;
		$this->boxrenderer   = $boxrenderer;
		$this->formrenderer  = $formrenderer;
		$this->postdata      = $postdata;
	}

	public function render_notifications() {

		foreach ( $this->notifications->get() as $notification ) {

			$this->postdata->set_notification_data( $notification );

			$this->formrenderer->set_fields( $notification->get_form_fields() );

			$this->boxrenderer->set_vars( array(
				'id'      => 'notification_type_' . $notification->get_slug(),
				'name'    => 'notification_' . $notification->get_slug() . '_enable',
				'title'   => $notification->get_name(),
				'content' => $this->formrenderer->render(),
				'open'    => $notification->enabled
			) );

			$this->boxrenderer->render();

		}

	}

	/**
	 * Save the notifications in post meta
	 * @param  integer $post_id current post ID
	 * @return void
	 */
	public function save( $post_id ) {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $this->postdata->set_post_id( $post_id );
        $this->postdata->save_notification_data( $_POST );

	}

}
