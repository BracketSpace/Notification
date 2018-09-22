<?php
/**
 * Handles Merge Tags metabox
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\View;
use BracketSpace\Notification\Utils\Ajax;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * MergeTags class
 */
class MergeTags {

	/**
	 * MergeTags constructor
	 *
	 * @since 5.0.0
	 * @param View $view View class.
	 * @param Ajax $ajax Ajax class.
	 */
	public function __construct( View $view, Ajax $ajax ) {
		$this->view = $view;
		$this->ajax = $ajax;
	}

	/**
	 * Add metabox for trigger
	 *
	 * @action add_meta_boxes
	 *
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

		// enable metabox.
		add_filter( 'notification/admin/allow_metabox/notification_merge_tags', '__return_true' );

	}

	/**
	 * Merge tags metabox content
	 *
	 * @param  object $post current WP_Post.
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
	 *
	 * @param  string $trigger_slug trigger slug.
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
	 *
	 * @param  Triggerable $trigger Trigger object.
	 * @return void
	 */
	public function merge_tags_list( Triggerable $trigger ) {

		$tags = $trigger->get_merge_tags( 'visible' );

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
	 *
	 * @action wp_ajax_get_merge_tags_for_trigger
	 *
	 * @return void
	 */
	public function ajax_render() {

		if ( ! isset( $_POST['trigger_slug'] ) ) {
			$this->ajax->error();
		}

		ob_start();

		$this->trigger_merge_tags_list( sanitize_text_field( wp_unslash( $_POST['trigger_slug'] ) ) );

		$this->ajax->success( ob_get_clean() );

	}

}
