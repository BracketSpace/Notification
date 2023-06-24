<?php
/**
 * Handles Post Type
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Store;
use BracketSpace\Notification\Dependencies\Micropackage\Ajax\Response;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;

/**
 * PostType class
 */
class PostType {

	/**
	 * TABLE OF CONTENTS: -------------------------------
	 * - Post Type.
	 * - Delete.
	 * - Save.
	 * - AJAX.
	 * - Notifications.
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
			'menu_icon'           => \Notification::fs()->image_to_base64( 'resources/images/menu-icon.svg' ),
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
	 * Filters the post updated messages
	 *
	 * @filter post_updated_messages
	 *
	 * @since  5.2.0
	 * @param  array $messages Messages.
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		$messages['notification'] = [
			'',
			__( 'Notification updated.', 'notification' ),
			'',
			'',
			__( 'Notification updated.', 'notification' ),
			'',
			__( 'Notification saved.', 'notification' ),
			__( 'Notification saved.', 'notification' ),
			'',
			'',
			'',
		];

		return $messages;
	}

	/**
	 * Filters the bulk action messages
	 *
	 * @filter bulk_post_updated_messages
	 *
	 * @since  6.0.0
	 * @param  array $bulk_messages Messages.
	 * @param  array $bulk_counts   Counters.
	 * @return array
	 */
	public function bulk_action_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages['notification'] = [
			// translators: Number of Notifications.
			'deleted' => _n( '%s notification removed.', '%s notifications removed.', $bulk_counts['trashed'] ),
		];

		return $bulk_messages;
	}

	/**
	 * Changes the notification post statuses above the post table
	 *
	 * @filter views_edit-notification
	 *
	 * @since  6.0.0
	 * @param  array $statuses Statuses array.
	 * @return array
	 */
	public function change_post_statuses( $statuses ) {
		if ( isset( $statuses['publish'] ) ) {
			$statuses['publish'] = str_replace( __( 'Published', 'notification' ), __( 'Active', 'notification' ), $statuses['publish'] );
		}

		if ( isset( $statuses['draft'] ) ) {
			$statuses['draft'] = str_replace( __( 'Draft', 'notification' ), __( 'Disabled', 'notification' ), $statuses['draft'] );
		}

		return $statuses;
	}

	/**
	 * --------------------------------------------------
	 * Delete.
	 * --------------------------------------------------
	 */

	/**
	 * Deletes the post entirely bypassing the trash
	 *
	 * @action wp_trash_post 100
	 *
	 * @since  6.0.0
	 * @param  integer $post_id Post ID.
	 * @return void
	 */
	public function bypass_trash( $post_id ) {
		if ( 'notification' !== get_post_type( $post_id ) ) {
			return;
		}

		wp_delete_post( $post_id, true );
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
	 * @since  6.0.0
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

		// Title.
		if ( isset( $data['post_title'] ) ) {
			$notification_post->set_title( $data['post_title'] );
		}

		// Status.
		$status = ( isset( $data['notification_onoff_switch'] ) && '1' === $data['notification_onoff_switch'] );
		$notification_post->set_enabled( $status );

		// Trigger.
		if ( ! empty( $data['notification_trigger'] ) ) {
			$trigger = Store\Trigger::get( $data['notification_trigger'] );
			if ( ! empty( $trigger ) ) {
				$notification_post->set_trigger( $trigger );
			}
		}

		// Prepare Carriers to save.
		$carriers = [];

		foreach ( Store\Carrier::all() as $carrier ) {
			if ( ! isset( $data[ 'notification_carrier_' . $carrier->get_slug() ] ) ) {
				continue;
			}

			$carrier_data = $data[ 'notification_carrier_' . $carrier->get_slug() ];

			if ( ! $carrier_data['activated'] ) {
				continue;
			}

			// If nonce not set or false, ignore this form.
			if ( ! wp_verify_nonce( $carrier_data['_nonce'], $carrier->get_slug() . '_carrier_security' ) ) {
				continue;
			}

			// @todo #h1kf7 `enabled` key is overwritten below.
			$carrier->set_data( $carrier_data );

			if ( isset( $data[ 'notification_carrier_' . $carrier->get_slug() . '_enable' ] ) ) {
				$carrier->enable();
			} else {
				$carrier->disable();
			}

			$carriers[ $carrier->get_slug() ] = $carrier;
		}

		$notification_post->set_carriers( $carriers );

		// Hook into this action if you want to save any Notification Post data.
		do_action( 'notification/data/save', $notification_post );

		$notification_post->save();

		/**
		 * @todo
		 * This cache should be cleared in Adapter save method.
		 * Now it's used in Admin\Wizard::add_notifications() as well
		 */
		$cache = new CacheDriver\ObjectCache( 'notification' );
		$cache->set_key( 'notifications' );
		$cache->delete();

		do_action( 'notification/data/save/after', $notification_post );
	}

	/**
	 * --------------------------------------------------
	 * AJAX.
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
		check_ajax_referer( 'notification_csrf' );

		$ajax  = new Response();
		$data  = $_POST;
		$error = false;

		$ajax->verify_nonce( 'change_notification_status_' . $data['post_id'] );

		$adapter = notification_adapt_from( 'WordPress', (int) $data['post_id'] );
		$adapter->set_enabled( 'true' === $data['status'] );

		$result = $adapter->save();

		if ( is_wp_error( $result ) ) {
			$ajax->error( __( 'Notification status couldn\'t be changed.', 'notification' ) );
		}

		$ajax->send( true );
	}

	/**
	 * --------------------------------------------------
	 * Notifications.
	 * --------------------------------------------------
	 */

	/**
	 * Gets all Notifications from database.
	 * Uses direct database call for performance.
	 *
	 * @since  6.0.0
	 * @return array
	 */
	public static function get_all_notifications() {
		$driver = new CacheDriver\ObjectCache( 'notification' );
		$cache  = new Cache( $driver, 'notifications' );

		return $cache->collect( function () {
			global $wpdb;

			$sql = "SELECT p.post_content
				FROM {$wpdb->posts} p
				WHERE p.post_type = 'notification' AND p.post_status = 'publish'
				ORDER BY p.menu_order ASC, p.post_modified DESC";

			// We're using direct db call for performance purposes - we only need the post_content field.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
			return $wpdb->get_col( $sql );
		} );
	}

	/**
	 * Sets up all the Notification from database
	 * It's running on every single page load.
	 *
	 * @action notification/init 9999999
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function setup_notifications() {
		$notifications = self::get_all_notifications();

		foreach ( $notifications as $notification_json ) {
			if ( ! empty( $notification_json ) ) {
				// Check if Notification has valid JSON.
				$json_check = json_decode( $notification_json, true );
				if ( json_last_error() !== JSON_ERROR_NONE ) {
					continue;
				}

				$adapter = notification_adapt_from( 'JSON', $notification_json );

				// Set source back to WordPress.
				$adapter->set_source( 'WordPress' );

				// Check if the notification hasn't been added already ie. via Sync.
				if ( ! Store\Notification::has( $adapter->get_hash() ) ) {
					notification_add( $adapter->get_notification() );
				}
			}
		}
	}

}
