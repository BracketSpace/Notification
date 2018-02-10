<?php
/**
 * Handles Trigger metabox
 *
 * @package notification
 */

namespace underDEV\Notification\Admin;

use underDEV\Notification\Utils\View;

/**
 * Trigger class
 */
class Trigger {

	/**
	 * MergeTags constructor
	 *
	 * @since [Next]
	 * @param View     $view     View class.
	 * @param PostData $postdata PostData class.
	 */
	public function __construct( View $view, PostData $postdata ) {
		$this->view     = $view;
		$this->postdata = $postdata;
	}

	/**
	 * Add metabox for trigger
     *
	 * @return void
	 */
	public function add_meta_box() {

		add_meta_box(
            'notification_trigger',
            __( 'Trigger', 'notification' ),
            array( $this, 'trigger_metabox' ),
            'notification',
            'after_subject',
            'high'
        );

		// enable metabox.
        add_filter( 'notification/admin/allow_metabox/notification_trigger', '__return_true' );

	}

	/**
	 * Prints trigger select
     *
	 * @param  object $post current WP_Post.
	 * @return void
	 */
	public function render_select( $post ) {

		$this->view->set_var( 'post', $post );
		$this->view->set_var( 'selected', $this->postdata->get_active_trigger() );
		$this->view->set_var( 'triggers', notification_get_triggers_grouped() );
		$this->view->set_var( 'select_name', 'notification_trigger' );

		$this->view->get_view( 'trigger/metabox' );

	}

	/**
	 * Save the trigger in post meta (key: _trigger)
     *
	 * @param  integer $post_id current post ID.
	 * @return void
	 */
	public function save( $post_id ) {

        if ( ! isset( $_POST['trigger_nonce'] ) || ! wp_verify_nonce( $_POST['trigger_nonce'], 'notification_trigger' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $this->postdata->set_post_id( $post_id );
        $this->postdata->save_active_trigger( $_POST['notification_trigger'] );

	}

}
