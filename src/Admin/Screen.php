<?php
/**
 * Screen class
 * Renders the Notification screens.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Store;
use BracketSpace\Notification\Dependencies\Micropackage\Ajax\Response;

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
	 * @param  \WP_Post $post Post object.
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
		$grouped_triggers = Store\Trigger::grouped();
		$trigger          = $notification_post->get_trigger();

		// Add merge tags.
		if ( $trigger ) {
			$trigger->setup_merge_tags();
		}

		Templates::render( 'trigger/metabox', [
			'selected'     => $trigger ? $trigger->get_slug() : '',
			'triggers'     => $grouped_triggers,
			'has_triggers' => ! empty( $grouped_triggers ),
			'select_name'  => 'notification_trigger',
			'notification' => $notification_post,
		] );
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

		foreach ( Store\Carrier::all() as $_carrier ) {

			$carrier = $notification_post->get_carrier( $_carrier->get_slug() );

			// If Carrier wasn't set before, use the blank one.
			if ( ! $carrier ) {
				$carrier = $_carrier;
			}

			// If Carrier is enabled then it must be activated as well.
			// Fix for previous versions when enabled but not activated Carrier was just wiped out.
			if ( $carrier->is_enabled() ) {
				$carrier->activate();
			}

			Templates::render( 'box', [
				'slug'        => $carrier->get_slug(),
				'carrier'     => $carrier,
				'id'          => 'notification-carrier-' . $carrier->get_slug() . '-box',
				'name'        => 'notification_carrier_' . $carrier->get_slug() . '_enable',
				'active_name' => 'notification_carrier_' . $carrier->get_slug() . '_active',
				'title'       => $carrier->get_name(),
				'content'     => $this->get_carrier_form( $carrier ),
				'open'        => $carrier->is_enabled(),
				'active'      => $carrier->is_active(),
			] );

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
		$carriers = Store\Carrier::all();
		$exists   = $notification_post->get_carriers();

		Templates::render( 'carriers/widget-add', [
			'carriers_added_count'  => count( $carriers ),
			'carriers_exists_count' => count( $exists ),
			'carriers'              => $carriers,
			'carriers_exists'       => (array) $exists,
		] );
	}

	/**
	 * Gets Carrier config form
	 *
	 * @since  6.0.0
	 * @param  Interfaces\Sendable $carrier Carrier object.
	 * @return string                       Form HTML.
	 */
	public function get_carrier_form( Interfaces\Sendable $carrier ) {
		$fields = $carrier->get_form_fields();

		// No fields available so return the default view.
		if ( empty( $fields ) && ! $carrier->has_recipients_field() ) {
			return Templates::get( 'form/empty-form' );
		}

		// Setup the fields and return form.
		return Templates::get( 'form/table', [
			'carrier' => $carrier,
		] );
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
		if ( ! EMPTY_TRASH_DAYS ) {
			$delete_text = __( 'Delete Permanently', 'notification' );
		} else {
			$delete_text = __( 'Move to Trash', 'notification' );
		}

		// New posts has the status auto-draft and in this case the Notification should be enabled.
		$enabled = 'draft' !== get_post_status( $post->ID );

		Templates::render( 'save-metabox', [
			'enabled'           => $enabled,
			'post_id'           => $post->ID,
			'delete_link_label' => $delete_text,
		] );
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
		$notification = notification_adapt_from( 'WordPress', $post );
		$trigger      = $notification->get_trigger();
		$trigger_slug = $trigger ? $trigger->get_slug() : false;

		if ( ! $trigger_slug ) {
			Templates::render( 'mergetag/metabox-notrigger' );
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
		$trigger = Store\Trigger::get( $trigger_slug );

		if ( empty( $trigger ) ) {
			Templates::render( 'mergetag/metabox-nomergetags' );
			return;
		}

		$tag_groups = $this->prepare_merge_tag_groups( $trigger );

		if ( empty( $tag_groups ) ) {
			Templates::render( 'mergetag/metabox-nomergetags' );
			return;
		}

		$vars = [
			'trigger'    => $trigger,
			'tags'       => $trigger->get_merge_tags( 'visible' ),
			'tag_groups' => $tag_groups,
		];

		if ( count( $tag_groups ) > 1 ) {
			Templates::render( 'mergetag/metabox-accordion', $vars );
		} else {
			Templates::render( 'mergetag/metabox-list', $vars );
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

		$screen->add_help_tab( [
			'id'      => 'notification_global_merge_tags',
			'title'   => __( 'Global Merge Tags', 'notification' ),
			'content' => Templates::get( 'help/global-merge-tags', [
				'tags' => Store\GlobalMergeTag::all(),
			] ),
		] );

		$screen->set_help_sidebar( Templates::get( 'help/sidebar' ) );
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
		check_ajax_referer( 'notification_csrf' );

		$ajax = new Response();

		if ( ! isset( $_POST['trigger_slug'] ) ) {
			$ajax->error();
		}

		ob_start();

		$this->render_merge_tags_list( sanitize_text_field( wp_unslash( $_POST['trigger_slug'] ) ) );

		$ajax->send( ob_get_clean() );
	}
}
