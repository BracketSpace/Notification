<?php
/**
 * Screen class
 * Renders the Notification screens.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Interfaces;

/**
 * Screen class
 */
class Screen {

	/**
	 * TABLE OF CONTENTS: -------------------------------
	 * - Display.
	 * - Screen Help.
	 * - AJAX Renders.
	 * --------------------------------------------------
	 */

	/**
	 * --------------------------------------------------
	 * Display.
	 * --------------------------------------------------
	 */

	/**
	 * Renders main column on the Notification edit screen.
	 *
	 * @action edit_form_after_title 1
	 *
	 * @param  object $post WP_Post.
	 * @return void
	 */
	public function render_main_column( $post ) {

		if ( 'notification' !== get_post_type( $post ) ) {
			return;
		}

		$notification_post = notification_adapt_from( 'WordPress', $post );

		do_action( 'notification/post/column/main', $notification_post );

	}

	/**
	 * Renders the trigger metabox
	 *
	 * @action notification/post/column/main
	 *
	 * @param  Notification $notification_post Notification Post object.
	 * @return void
	 */
	public function render_trigger_select( $notification_post ) {

		$view             = notification_create_view();
		$grouped_triggers = notification_get_triggers_grouped();
		$trigger          = $notification_post->get_trigger();

		$view->set_var( 'selected', $trigger ? $trigger->get_slug() : '' );
		$view->set_var( 'triggers', $grouped_triggers );
		$view->set_var( 'has_triggers', ! empty( $grouped_triggers ) );
		$view->set_var( 'select_name', 'notification_trigger' );
		$view->set_var( 'notification', $notification_post );

		$view->get_view( 'trigger/metabox' );

	}

	/**
	 * Adds Carriers section title on post edit screen,
	 * just under the Trigger and prints Carrier boxes
	 *
	 * @action notification/post/column/main 20
	 *
	 * @param  Notification $notification_post Notification Post object.
	 * @return void
	 */
	public function render_carrier_boxes( $notification_post ) {

		echo '<h3 class="carriers-section-title">' . esc_html__( 'Carriers', 'notification' ) . '</h3>';

		do_action_deprecated( 'notitication/admin/notifications/pre', [
			$notification_post,
		], '6.0.0', 'notification/admin/carriers/pre' );

		do_action( 'notification/admin/carriers/pre', $notification_post );

		echo '<div id="carrier-boxes">';

		foreach ( notification_get_carriers() as $_carrier ) {

			$box_view = notification_create_view();
			$carrier  = $notification_post->get_carrier( $_carrier->get_slug() );

			// If Carrier wasn't set before, use the blank one.
			if ( ! $carrier ) {
				$carrier = $_carrier;
			}

			// If Carrier is enabled then it must be activated as well.
			// Fix for previous versions when enabled but not activated Carrier was just wiped out.
			if ( $carrier->is_enabled() ) {
				$carrier->activate();
			}

			$box_view->set_vars( [
				'slug'        => $carrier->get_slug(),
				'id'          => 'notification-carrier-' . $carrier->get_slug() . '-box',
				'name'        => 'notification_carrier_' . $carrier->get_slug() . '_enable',
				'active_name' => 'notification_carrier_' . $carrier->get_slug() . '_active',
				'title'       => $carrier->get_name(),
				'content'     => $this->get_carrier_form( $carrier ),
				'open'        => $carrier->is_enabled(),
				'active'      => $carrier->is_active(),
			] );

			$box_view->get_view( 'box' );

		}

		echo '</div>';

		do_action_deprecated( 'notitication/admin/notifications', [
			$notification_post,
		], '6.0.0', 'notification/admin/carriers' );

		do_action( 'notification/admin/carriers', $notification_post );

	}

	/**
	 * Renders a widget for adding Carriers
	 *
	 * @action notification/admin/carriers
	 *
	 * @param  object $notification_post Notification Post object.
	 * @return void
	 */
	public function render_carriers_widget( $notification_post ) {

		$carriers = notification_get_carriers();
		$exists   = $notification_post->get_carriers();

		$view = notification_create_view();
		$view->set_vars( [
			'carriers_added_count'  => count( $carriers ),
			'carriers_exists_count' => count( $exists ),
			'carriers'              => $carriers,
			'carriers_exists'       => $exists,
		] );
		$view->get_view( 'carriers/widget-add' );

	}

	/**
	 * Gets Carrier config form
	 *
	 * @since  6.0.0
	 * @param  Interfaces\Sendable $carrier Carrier object.
	 * @return string                       Form HTML.
	 */
	public function get_carrier_form( Interfaces\Sendable $carrier ) {

		$view   = notification_create_view();
		$fields = $carrier->get_form_fields();

		// No fields available so return the default view.
		if ( empty( $fields ) ) {
			return $view->get_view_output( 'form/empty-form' );
		}

		// Setup the fields and return form.
		$view->set_var( 'fields', $fields );
		return $view->get_view_output( 'form/table' );

	}

	/**
	 * Adds metabox with Save button
	 *
	 * @action add_meta_boxes
	 *
	 * @return void
	 */
	public function add_save_meta_box() {

		add_meta_box(
			'notification_save',
			__( 'Save', 'notification' ),
			[ $this, 'render_save_metabox' ],
			'notification',
			'side',
			'high'
		);

		// enable metabox.
		add_filter( 'notification/admin/allow_metabox/notification_save', '__return_true' );

	}

	/**
	 * Renders Save metabox
	 *
	 * @param  object $post current WP_Post.
	 * @return void
	 */
	public function render_save_metabox( $post ) {

		$view = notification_create_view();

		if ( ! EMPTY_TRASH_DAYS ) {
			$delete_text = __( 'Delete Permanently', 'notification' );
		} else {
			$delete_text = __( 'Move to Trash', 'notification' );
		}

		// New posts has the status auto-draft and in this case the Notification should be enabled.
		$enabled = 'draft' !== get_post_status( $post->ID );

		$view->set_var( 'enabled', $enabled );
		$view->set_var( 'post_id', $post->ID );
		$view->set_var( 'delete_link_label', $delete_text );

		$view->get_view( 'save-metabox' );

	}

	/**
	 * Adds metabox with Merge Tags.
	 *
	 * @action add_meta_boxes
	 *
	 * @return void
	 */
	public function add_merge_tags_meta_box() {

		add_meta_box(
			'notification_merge_tags',
			__( 'Merge Tags', 'notification' ),
			[ $this, 'render_merge_tags_metabox' ],
			'notification',
			'side',
			'default'
		);

		// enable metabox.
		add_filter( 'notification/admin/allow_metabox/notification_merge_tags', '__return_true' );

	}

	/**
	 * Renders Merge Tags metabox
	 *
	 * @param  object $post current WP_Post.
	 * @return void
	 */
	public function render_merge_tags_metabox( $post ) {

		$view         = notification_create_view();
		$notification = notification_adapt_from( 'WordPress', $post );
		$trigger      = $notification->get_trigger();
		$trigger_slug = $trigger ? $trigger->get_slug() : false;

		if ( ! $trigger_slug ) {
			$view->get_view( 'mergetag/metabox-notrigger' );
			return;
		}

		$this->render_merge_tags_list( $trigger_slug );

	}

	/**
	 * Renders Merge Tags list
	 *
	 * @param  string $trigger_slug Trigger slug.
	 * @return void
	 */
	public function render_merge_tags_list( $trigger_slug ) {

		$view    = notification_create_view();
		$trigger = notification_get_trigger( $trigger_slug );

		if ( empty( $trigger ) ) {
			$view->get_view( 'mergetag/metabox-nomergetags' );
			return;
		}

		$tag_groups = $this->prepare_merge_tag_groups( $trigger );

		if ( empty( $tag_groups ) ) {
			$view->get_view( 'mergetag/metabox-nomergetags' );
			return;
		}

		$view->set_var( 'trigger', $trigger );
		$view->set_var( 'tags', $trigger->get_merge_tags( 'visible' ) );
		$view->set_var( 'tag_groups', $tag_groups );

		if ( count( $tag_groups ) > 1 ) {
			$view->get_view( 'mergetag/metabox-accordion' );
		} else {
			$view->get_view( 'mergetag/metabox-list' );
		}
	}

	/**
	 * Prepates merge tag groups for provided Trigger.
	 *
	 * @param  object $trigger Trigger object.
	 * @return array  $groups  Grouped tags.
	 */
	public function prepare_merge_tag_groups( $trigger ) {

		$groups = [];
		$tags   = $trigger->get_merge_tags( 'visible' );

		if ( empty( $tags ) ) {
			return $groups;
		}

		$other_key = __( 'Other', 'notification' );

		foreach ( $tags as $tag ) {
			if ( $tag->get_group() ) {
				$groups[ $tag->get_group() ][] = $tag;
			} else {
				$groups[ $other_key ][] = $tag;
			}
		}

		ksort( $groups );

		if ( isset( $groups[ $other_key ] ) ) {
			$others = $groups[ $other_key ];
			unset( $groups[ $other_key ] );
			$groups[ $other_key ] = $others;
		}

		return apply_filters( 'notification/trigger/tags/groups', $groups, $trigger );

	}

	/**
	 * Cleans up all metaboxes to keep the screen nice and clean
	 *
	 * @action add_meta_boxes 999999999
	 *
	 * @return void
	 */
	public function metabox_cleanup() {

		global $wp_meta_boxes;

		if ( ! isset( $wp_meta_boxes['notification'] ) ) {
			return;
		}

		foreach ( $wp_meta_boxes['notification'] as $context_name => $context ) {
			foreach ( $context as $priority => $boxes ) {
				foreach ( $boxes as $box_id => $box ) {
					$allow_box = apply_filters( 'notification/admin/allow_metabox/' . $box_id, false );

					if ( ! $allow_box ) {
						unset( $wp_meta_boxes['notification'][ $context_name ][ $priority ][ $box_id ] );
					}
				}
			}
		}

	}

	/**
	 * --------------------------------------------------
	 * Screen Help.
	 * --------------------------------------------------
	 */

	/**
	 * Adds help tabs and useful links
	 *
	 * @action current_screen
	 *
	 * @param  object $screen current WP_Screen.
	 * @return void
	 */
	public function add_help( $screen ) {

		if ( 'notification' !== $screen->post_type ) {
			return;
		}

		$view = notification_create_view();

		$view->set_var( 'tags', notification_get_global_merge_tags() );

		$screen->add_help_tab( [
			'id'      => 'notification_global_merge_tags',
			'title'   => __( 'Global Merge Tags', 'notification' ),
			'content' => $view->get_view_output( 'help/global-merge-tags' ),
		] );

		$screen->set_help_sidebar( $view->get_view_output( 'help/sidebar' ) );

	}

	/**
	 * --------------------------------------------------
	 * AJAX Renders.
	 * --------------------------------------------------
	 */

	/**
	 * Renders Merge Tags metabox for AJAX call.
	 *
	 * @action wp_ajax_get_merge_tags_for_trigger
	 *
	 * @return void
	 */
	public function ajax_render_merge_tags() {

		$ajax = notification_ajax_handler();

		if ( ! isset( $_POST['trigger_slug'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$ajax->error();
		}

		ob_start();

		$this->render_merge_tags_list( sanitize_text_field( wp_unslash( $_POST['trigger_slug'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$ajax->success( ob_get_clean() );

	}

	/**
	 * Renders recipient input for AJAX call.
	 *
	 * @action wp_ajax_get_recipient_input
	 *
	 * @return void
	 */
	public function ajax_get_recipient_input() {

		ob_start();

		$carrier   = sanitize_text_field( wp_unslash( $_POST['carrier'] ) ); // phpcs:ignore
		$type      = sanitize_text_field( wp_unslash( $_POST['type'] ) ); // phpcs:ignore
		$recipient = notification_get_recipient( $carrier, $type );
		$input     = $recipient->input();

		// A little trick to get rid of the last part of input name
		// which will be added by the field itself.
		$input_name     = sanitize_text_field( wp_unslash( $_POST['input_name'] ) ); // phpcs:ignore
		$input->section = str_replace( '[' . $input->get_raw_name() . ']', '', $input_name );

		echo $input->field(); // phpcs:ignore

		$description = $input->get_description();
		if ( ! empty( $description ) ) {
			echo '<small class="description">' . $description . '</small>'; // phpcs:ignore
		}

		notification_ajax_handler()->success( ob_get_clean() );

	}

}
