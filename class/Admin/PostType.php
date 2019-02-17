<?php
/**
 * Handles Post Type
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\Notification;

/**
 * PostType class
 */
class PostType {

	/**
	 * TABLE OF CONTENTS: -------------------------------
	 * - Post Type.
	 * - Save.
	 * - AJAX.
	 * --------------------------------------------------
	 */

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

		$labels = [
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
		];

		register_post_type( 'notification', [
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
			'capabilities'        => apply_filters( 'notification/post_type/capabilities', [
				'edit_post'          => 'manage_options',
				'read_post'          => 'manage_options',
				'delete_post'        => 'manage_options',
				'edit_posts'         => 'manage_options',
				'edit_others_posts'  => 'manage_options',
				'delete_posts'       => 'manage_options',
				'publish_posts'      => 'manage_options',
				'read_private_posts' => 'manage_options',
			] ),
			'supports'            => [ 'title' ],
		] );

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

		$messages['notification'] = [
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
		];

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
	 * Save.
	 * --------------------------------------------------
	 */

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

		// Another save process is in progress, abort.
		if ( defined( 'DOING_NOTIFICATION_SAVE' ) && DOING_NOTIFICATION_SAVE ) {
			return $data;
		}

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

		// Another save process is in progress, abort.
		if ( defined( 'DOING_NOTIFICATION_SAVE' ) && DOING_NOTIFICATION_SAVE ) {
			return;
		}

		if ( ! isset( $_POST['notification_data_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['notification_data_nonce'] ) ), 'notification_post_data_save' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! $update ) {
			return;
		}

		// Prevent infinite loops.
		if ( ! defined( 'DOING_NOTIFICATION_SAVE' ) ) {
			define( 'DOING_NOTIFICATION_SAVE', true );
		}

		$data              = $_POST;
		$notification_post = notification_adapt_from( 'WordPress', $post );

		// Status.
		$status = ( isset( $data['notification_onoff_switch'] ) && '1' === $data['notification_onoff_switch'] );
		$notification_post->set_enabled( $status );

		// Trigger.
		if ( ! empty( $data['notification_trigger'] ) ) {
			$trigger = notification_get_trigger( $data['notification_trigger'] );
			if ( ! empty( $trigger ) ) {
				$notification_post->set_trigger( $trigger );
			}
		}

		// Prepare Carriers to save.
		$carriers = [];

		foreach ( notification_get_carriers() as $carrier ) {

			if ( ! isset( $data[ 'notification_carrier_' . $carrier->get_slug() ] ) ) {
				continue;
			}

			if ( isset( $data[ 'notification_carrier_' . $carrier->get_slug() . '_enable' ] ) ) {
				$carrier->enabled = true;
			}

			$carrier_data = $data[ 'notification_carrier_' . $carrier->get_slug() ];

			// If nonce not set or false, ignore this form.
			if ( ! wp_verify_nonce( $carrier_data['_nonce'], $carrier->get_slug() . '_carrier_security' ) ) {
				continue;
			}

			$carrier->set_data( $carrier_data );
			$carriers[ $carrier->get_slug() ] = $carrier;

		}

		$notification_post->set_carriers( $carriers );

		// Hook into this action if you want to save any Notification Post data.
		do_action( 'notification/data/save', $notification_post );

		$notification_post->save();

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

		$ajax  = notification_ajax_handler();
		$data  = $_POST; // phpcs:ignore
		$error = false;

		$ajax->verify_nonce( 'change_notification_status_' . $data['post_id'] );

		$status = 'true' === $data['status'] ? 'publish' : 'draft';

		$result = wp_update_post( [
			'ID'          => $data['post_id'],
			'post_status' => $status,
		] );

		if ( 0 === $result ) {
			$error = __( 'Notification status couldn\'t be changed.', 'notification' );
		}

		$ajax->response( true, $error );

	}

}
