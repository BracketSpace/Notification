<?php
/**
 * Handles Post Type
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\View;
use BracketSpace\Notification\Utils\Ajax;
use BracketSpace\Notification\Core\Notification;

/**
 * PostType class
 */
class PostType {

	/**
	 * PostType constructor
	 *
	 * @since 5.0.0
	 * @since [Next] Simplified the parameters by including separate classes elements here.
	 *
	 * @param Ajax         $ajax          Ajax class.
	 * @param BoxRenderer  $boxrenderer  BoxRenderer class.
	 * @param FormRenderer $formrenderer FormRenderer class.
	 */
	public function __construct( Ajax $ajax, BoxRenderer $boxrenderer, FormRenderer $formrenderer ) {
		$this->ajax         = $ajax;
		$this->boxrenderer  = $boxrenderer;
		$this->formrenderer = $formrenderer;
	}

	/**
	 * --------------------------------------------------
	 * Post Type.
	 * --------------------------------------------------
	 */

	/**
	 * Registers Notification post type
	 *
	 * @action init
	 *
	 * @return void
	 */
	public function register() {

		$labels = array(
			'name'               => __( 'Notifications', 'notification' ),
			'singular_name'      => __( 'Notification', 'notification' ),
			'add_new'            => _x( 'Add New Notification', 'notification', 'notification' ),
			'add_new_item'       => __( 'Add New Notification', 'notification' ),
			'edit_item'          => __( 'Edit Notification', 'notification' ),
			'new_item'           => __( 'New Notification', 'notification' ),
			'view_item'          => __( 'View Notification', 'notification' ),
			'search_items'       => __( 'Search Notifications', 'notification' ),
			'not_found'          => __( 'No Notifications found', 'notification' ),
			'not_found_in_trash' => __( 'No Notifications found in Trash', 'notification' ),
			'parent_item_colon'  => __( 'Parent Notification:', 'notification' ),
			'menu_name'          => __( 'Notifications', 'notification' ),
		);

		register_post_type(
			'notification',
			array(
				'labels'              => apply_filters( 'notification/whitelabel/cpt/labels', $labels ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => apply_filters( 'notification/whitelabel/cpt/parent', true ),
				'show_in_admin_bar'   => true,
				'menu_icon'           => 'dashicons-megaphone',
				'menu_position'       => 103,
				'show_in_nav_menus'   => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => false,
				'can_export'          => true,
				'rewrite'             => false,
				'capabilities'        => apply_filters(
					'notification/post_type/capabilities',
					array(
						'edit_post'          => 'manage_options',
						'read_post'          => 'manage_options',
						'delete_post'        => 'manage_options',
						'edit_posts'         => 'manage_options',
						'edit_others_posts'  => 'manage_options',
						'delete_posts'       => 'manage_options',
						'publish_posts'      => 'manage_options',
						'read_private_posts' => 'manage_options',
					)
				),
				'supports'            => array( 'title' ),
			)
		);

	}

	/**
	 * Filters the posu updated messages
	 *
	 * @filter post_updated_messages
	 *
	 * @since  5.2.0
	 * @param  array $messages Messages.
	 * @return array
	 */
	public function post_updated_messages( $messages ) {

		$post = get_post();

		$messages['notification'] = array(
			0  => '',
			1  => __( 'Notification updated.', 'notification' ),
			2  => '',
			3  => '',
			4  => __( 'Notification updated.', 'notification' ),
			5  => '',
			6  => __( 'Notification saved.', 'notification' ),
			7  => __( 'Notification saved.', 'notification' ),
			8  => '',
			9  => '',
			10 => '',
		);

		return $messages;

	}

	/**
	 * Changes the notification post statuses above the post table
	 *
	 * @filter views_edit-notification
	 *
	 * @since  [Next]
	 * @param  array $statuses Statuses array.
	 * @return array
	 */
	public function change_post_statuses( $statuses ) {

		if ( isset( $statuses['publish'] ) ) {
			$statuses['publish'] = str_replace( __( 'Published', 'wordpress' ), __( 'Active', 'notification' ), $statuses['publish'] ); // phpcs:ignore
		}

		if ( isset( $statuses['draft'] ) ) {
			$statuses['draft'] = str_replace( __( 'Draft', 'wordpress' ), __( 'Disabled', 'notification' ), $statuses['draft'] ); // phpcs:ignore
		}

		return $statuses;

	}

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

		$view->set_var( 'selected', $notification_post->get_trigger() );
		$view->set_var( 'triggers', $grouped_triggers );
		$view->set_var( 'has_triggers', ! empty( $grouped_triggers ) );
		$view->set_var( 'select_name', 'notification_trigger' );

		$view->get_view( 'trigger/metabox' );

	}

	/**
	 * Adds Notifications section title on post edit screen,
	 * just under the Trigger and prints Notification boxes
	 *
	 * @action notification/post/column/main 20
	 *
	 * @param  Notification $notification_post Notification Post object.
	 * @return void
	 */
	public function render_notification_boxes( $notification_post ) {

		echo '<h3 class="notifications-section-title">' . esc_html__( 'Notifications', 'notification' ) . '</h3>';

		do_action( 'notitication/admin/notifications/pre', $notification_post );

		echo '<div id="notification-boxes">';

		foreach ( notification_get_notifications() as $notification ) {

			$notification = $notification_post->populate_notification( $notification );

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

		echo '</div>';

		do_action( 'notitication/admin/notifications', $notification_post );

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
			array( $this, 'render_save_metabox' ),
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

		$enabled = notification_post_is_new( $post ) || 'draft' !== get_post_status( $post->ID );

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
			__( 'Merge tags', 'notification' ),
			array( $this, 'render_merge_tags_metabox' ),
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
		$trigger_slug = $notification->get_trigger();

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
		$trigger = notification_get_single_trigger( $trigger_slug );

		if ( empty( $trigger ) ) {
			$view->get_view( 'mergetag/metabox-nomergetags' );
			return;
		}

		$tags = $trigger->get_merge_tags( 'visible' );

		if ( empty( $tags ) ) {
			$view->get_view( 'mergetag/metabox-nomergetags' );
			return;
		}

		$view->set_var( 'trigger', $trigger );
		$view->set_var( 'tags', $tags );

		$view->get_view( 'mergetag/metabox' );

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
	 * Save.
	 * --------------------------------------------------
	 */

	/**
	 * Saves post status in relation to on/off switch
	 *
	 * @filter wp_insert_post_data 100
	 *
	 * @since  5.0.0
	 * @param  array $data    post data.
	 * @param  array $postarr saved data.
	 * @return array
	 */
	public function save_notification_status( $data, $postarr ) {

		// fix for brand new posts.
		if ( 'auto-draft' === $data['post_status'] ) {
			return $data;
		}

		// fix for AJAX calls.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $data;
		}

		if ( 'notification' !== $data['post_type'] ||
			'trash' === $postarr['post_status'] ||
			( isset( $_POST['action'] ) && 'change_notification_status' === $_POST['action'] ) ) { // phpcs:ignore
			return $data;
		}

		if ( isset( $postarr['onoffswitch'] ) && '1' === $postarr['onoffswitch'] ) {
			$data['post_status'] = 'publish';
		} else {
			$data['post_status'] = 'draft';
		}

		return $data;

	}

	/**
	 * Creates Notification unique hash
	 *
	 * @filter wp_insert_post_data 100
	 *
	 * @since  [Next]
	 * @param  array $data    post data.
	 * @param  array $postarr saved data.
	 * @return array
	 */
	public function create_notification_hash( $data, $postarr ) {

		if ( 'notification' !== $data['post_type'] ) {
			return $data;
		}

		if ( ! preg_match( '/notification_[a-z0-9]{13}/', $data['post_name'] ) ) {
			$data['post_name'] = Notification::create_hash();
		}

		return $data;

	}

	/**
	 * Saves the Notification data
	 *
	 * @action save_post_notification
	 *
	 * @param  integer $post_id Current post ID.
	 * @param  object  $post    WP_Post object.
	 * @param  boolean $update  If existing notification is updated.
	 * @return void
	 */
	public function save( $post_id, $post, $update ) {

		if ( ! isset( $_POST['notification_data_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['notification_data_nonce'] ) ), 'notification_post_data_save' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! $update ) {
			return;
		}

		$data              = $_POST;
		$notification_post = notification_adapt_from( 'WordPress', $post );

		// Trigger.
		if ( ! empty( $data['notification_trigger'] ) ) {
			$trigger = notification_get_single_trigger( $data['notification_trigger'] );
			if ( ! empty( $trigger ) ) {
				$notification_post->set_trigger( $trigger );
			}
		}

		// Prepare Carriers to save.
		$carriers = [];

		foreach ( notification_get_notifications() as $carrier ) {

			if ( ! isset( $data[ 'notification_type_' . $carrier->get_slug() ] ) ) {
				continue;
			}

			if ( isset( $data[ 'notification_' . $carrier->get_slug() . '_enable' ] ) ) {
				$carrier->enable = true;
			}

			$carrier_data = $data[ 'notification_type_' . $carrier->get_slug() ];

			// If nonce not set or false, ignore this form.
			if ( ! wp_verify_nonce( $carrier_data['_nonce'], $carrier->get_slug() . '_notification_security' ) ) {
				continue;
			}

			$carrier->set_data( $carrier_data );
			$carriers[] = $carrier;

		}

		$notification_post->set_notifications( $carriers );

		// Hook into this action if you want to save any Notification Post data.
		do_action( 'notification/data/save', $notification_post );

	}

	/**
	 * --------------------------------------------------
	 * Ajax.
	 * --------------------------------------------------
	 */

	/**
	 * Changes notification status from AJAX call
	 *
	 * @action wp_ajax_change_notification_status
	 *
	 * @return void
	 */
	public function ajax_change_notification_status() {

		$data  = $_POST; // phpcs:ignore
		$error = false;

		$this->ajax->verify_nonce( 'change_notification_status_' . $data['post_id'] );

		$status = 'true' === $data['status'] ? 'publish' : 'draft';

		$result = wp_update_post(
			array(
				'ID'          => $data['post_id'],
				'post_status' => $status,
			)
		);

		if ( 0 === $result ) {
			$error = __( 'Notification status couldn\'t be changed.', 'notification' );
		}

		$this->ajax->response( true, $error );

	}

	/**
	 * Renders Merge Tags metabox for AJAX call.
	 *
	 * @action wp_ajax_get_merge_tags_for_trigger
	 *
	 * @return void
	 */
	public function ajax_render_merge_tags() {

		if ( ! isset( $_POST['trigger_slug'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$this->ajax->error();
		}

		ob_start();

		$this->render_merge_tags_list( sanitize_text_field( wp_unslash( $_POST['trigger_slug'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$this->ajax->success( ob_get_clean() );

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

		$notification = sanitize_text_field( wp_unslash( $_POST['notification'] ) ); // phpcs:ignore
		$type         = sanitize_text_field( wp_unslash( $_POST['type'] ) ); // phpcs:ignore
		$recipient    = notification_get_single_recipient( $notification, $type );
		$input        = $recipient->input();

		// A little trick to get rid of the last part of input name
		// which will be added by the field itself.
		$input_name     = sanitize_text_field( wp_unslash( $_POST['input_name'] ) ); // phpcs:ignore
		$input->section = str_replace( '[' . $input->get_raw_name() . ']', '', $input_name );

		echo $input->field(); // phpcs:ignore

		$description = $input->get_description();
		if ( ! empty( $description ) ) {
			echo '<small class="description">' . $description . '</small>'; // phpcs:ignore
		}

		$this->ajax->success( ob_get_clean() );

	}

}
