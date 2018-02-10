<?php
/**
 * Handles Merge Tags metabox
 */

namespace underDEV\Notification\Admin;
use underDEV\Notification\Utils;
use underDEV\Notification\Interfaces\Triggerable;

class MergeTags {

	public function __construct( Utils\View $view, Utils\Ajax $ajax ) {
		$this->view = $view;
		$this->ajax = $ajax;
	}

	/**
	 * Add metabox for trigger
	 * @return void
	 */
	public function add_meta_box() {

		add_meta_box(
            'notification_merge_tags',
            __( 'Merge tags', 'notification' ),
            array( $this, 'merge_tags_metabox' ),
            'notification',
            'side',
            'default'
        );

		// enable metabox
        add_filter( 'notification/admin/allow_metabox/notification_merge_tags', '__return_true' );

	}

	/**
	 * Merge tags metabox content
	 * @param  object $post current WP_Post
	 * @return void
	 */
	public function merge_tags_metabox( $post ) {

		$trigger_slug = get_post_meta( $post->ID, '_trigger', true );

		if ( ! $trigger_slug ) {
			$this->view->get_view( 'mergetag/metabox-notrigger' );
			return;
		}

		$this->trigger_merge_tags_list( $trigger_slug );

	}

	/**
	 * Prints merge tags list for trigger
	 * @param  string $trigger_slug trigger slug
	 * @return void
	 */
	public function trigger_merge_tags_list( $trigger_slug ) {

		$trigger = notification_get_single_trigger( $trigger_slug );

		if ( empty( $trigger ) ) {
			$this->view->get_view( 'mergetag/metabox-nomergetags' );
			return;
		}

		$this->merge_tags_list( $trigger );

	}

	/**
	 * Prints merge tags list
	 * @param  object $trigger Trigger object
	 * @return void
	 */
	public function merge_tags_list( Triggerable $trigger ) {

		$tags = $trigger->get_merge_tags();

		if ( empty( $tags ) ) {
			$this->view->get_view( 'mergetag/metabox-nomergetags' );
			return;
		}

		$this->view->set_var( 'trigger', $trigger );
		$this->view->set_var( 'tags', $tags );

		$this->view->get_view( 'mergetag/metabox' );

	}

	/**
	 * Renders metabox for AJAX
	 * @return void
	 */
	public function ajax_render() {

		ob_start();

		$this->trigger_merge_tags_list( $_POST['trigger_slug'] );

		$this->ajax->success( ob_get_clean() );

	}

}
