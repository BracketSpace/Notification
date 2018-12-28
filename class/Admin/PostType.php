<?php
/**
 * Handles Post Type
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\View;
use BracketSpace\Notification\Utils\Ajax;

/**
 * PostType class
 */
class PostType {

	/**
	 * PostType constructor
	 *
	 * @since 5.0.0
	 * @param Notifications $notifications Notifications class.
	 * @param Ajax          $ajax          Ajax class.
	 */
	public function __construct( Notifications $notifications, Ajax $ajax ) {
		$this->notifications = $notifications;
		$this->ajax          = $ajax;
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

		$notification_post = notification_get_post( $post );

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
	 * just under the Trigger and prints Notifications metaboxes
	 *
	 * @action notification/post/column/main 20
	 *
	 * @param  Notification $notification_post Notification Post object.
	 * @return void
	 */
	public function render_notification_metaboxes( $notification_post ) {

		echo '<h3 class="notifications-section-title">' . esc_html__( 'Notifications', 'notification' ) . '</h3>';

		do_action( 'notitication/admin/notifications/pre', $notification_post );

		echo '<div id="notification-boxes">';
			$this->notifications->render_notifications();
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
	 * Prints Save metabox
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

		$enabled = notification_is_new_notification( $post ) || 'draft' !== get_post_status( $post->ID );

		$view->set_var( 'enabled', $enabled );
		$view->set_var( 'post_id', $post->ID );
		$view->set_var( 'delete_link_label', $delete_text );

		$view->get_view( 'save-metabox' );

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

		if ( 'notification' !== $data['post_type'] ||
			'trash' === $postarr['post_status'] ||
			( isset( $_POST['action'] ) && 'change_notification_status' === $_POST['action'] ) ) {
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

		$notification_post = notification_get_post( $post );

		// Trigger.
		$trigger = ! empty( $_POST['notification_trigger'] ) ? sanitize_text_field( wp_unslash( $_POST['notification_trigger'] ) ) : '';
		$notification_post->set_trigger( $trigger );

		// Hook into this action if you want to save any Notification Post data.
		do_action( 'notification/post/save', $notification_post );

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

		$data  = $_POST;
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

}
