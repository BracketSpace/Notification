<?php
/**
 * Handles Post Data
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Utils\Ajax;

/**
 * PostData class
 */
class PostData {

	/**
	 * Meta cache
	 *
	 * @var array
	 */
	protected $meta_cache = array();

	/**
	 * Current post ID
	 *
	 * @var integer
	 */
	protected $post_id = null;

	/**
	 * PostData constructor
	 *
	 * @since 5.0.0
	 * @param Ajax $ajax Ajax class.
	 */
	public function __construct( Ajax $ajax ) {

		$this->ajax = $ajax;

		$this->notification_enabled_key = '_enabled_notification';
		$this->notification_data_key    = '_notification_type_';
		$this->active_trigger_key       = '_trigger';

	}

	/**
	 * Gets current post ID
	 *
	 * @return integer post ID
	 */
	public function get_post_id() {

		if ( is_null( $this->post_id ) ) {
			global $post;
			$this->post_id = $post->ID;
		}

		return $this->post_id;

	}

	/**
	 * Sets current post ID
	 *
	 * @param  integer $post_id post ID.
	 * @return $this
	 */
	public function set_post_id( $post_id ) {
		$this->post_id = $post_id;
		return $this;
	}

	/**
	 * Clears previously set current post ID
	 *
	 * @return $this
	 */
	public function clear_post_id() {
		$this->post_id = null;
		return $this;
	}

	/**
	 * Gets post meta for key using internal cache
	 *
	 * @param  string  $key    meta key.
	 * @param  boolean $single if return only single val.
	 * @return mixed           meta value
	 */
	public function get_meta( $key, $single = true ) {

		if ( ! isset( $this->meta_cache[ $this->get_post_id() ], $this->meta_cache[ $this->get_post_id() ][ $key ], $this->meta_cache[ $key ][ $single ] ) ) {
			$this->meta_cache[ $this->get_post_id() ][ $key ][ $single ] = get_post_meta( $this->get_post_id(), $key, $single );
		}

		return $this->meta_cache[ $this->get_post_id() ][ $key ][ $single ];

	}

	/**
	 * Sets notification data from post meta
	 *
	 * @param Interfaces\Sendable $notification notification object.
	 * @return void
	 */
	public function set_notification_data( Interfaces\Sendable $notification ) {

		// set enabled state.
		$enabled_notifications = (array) $this->get_meta( $this->notification_enabled_key, false );

		// if this is new post, mark email notifiation as active for better UX.
		if ( notification_is_new_notification( get_post( $this->get_post_id() ) ) && $notification->get_slug() === 'email' ) {
			$enabled_notifications[] = 'email';
		}

		if ( in_array( $notification->get_slug(), $enabled_notifications, true ) ) {
			$notification->enabled = true;
		}

		// set current notification post ID.
		$notification->post_id = $this->get_post_id();

		// set field values.
		$field_values = (array) $this->get_meta( $this->notification_data_key . $notification->get_slug() );
		$field_values = apply_filters( 'notification/notification/form_fields/values', $field_values, $notification );

		foreach ( $notification->get_form_fields() as $field ) {

			if ( isset( $field_values[ $field->get_raw_name() ] ) ) {
				$field->set_value( $field_values[ $field->get_raw_name() ] );
			}
		}

	}

	/**
	 * Saves notifications data
	 *
	 * @param array $data user data to save.
	 * @return void
	 */
	public function save_notification_data( $data ) {

		// enable all notifications one by one.
		foreach ( notification_get_notifications() as $notification ) {

			if ( isset( $data[ 'notification_' . $notification->get_slug() . '_enable' ] ) ) {
				if ( ! in_array( $notification->get_slug(), (array) get_post_meta( $this->get_post_id(), $this->notification_enabled_key ), true ) ) {
					add_post_meta( $this->get_post_id(), $this->notification_enabled_key, $notification->get_slug() );
				}
			} else {
				delete_post_meta( $this->get_post_id(), $this->notification_enabled_key, $notification->get_slug(), $notification->get_slug() );
			}
		}

		// save all notification settings one by one.
		foreach ( notification_get_notifications() as $notification ) {

			if ( ! isset( $data[ 'notification_type_' . $notification->get_slug() ] ) ) {
				continue;
			}

			$ndata = $data[ 'notification_type_' . $notification->get_slug() ];

			// nonce not set or false, ignoring this form.
			if ( ! wp_verify_nonce( $ndata['_nonce'], $notification->get_slug() . '_notification_security' ) ) {
				continue;
			}

			$notification_data = array();

			// sanitize each field individually.
			foreach ( $notification->get_form_fields() as $field ) {

				if ( isset( $ndata[ $field->get_raw_name() ] ) ) {
					$user_data = $ndata[ $field->get_raw_name() ];
				} else {
					$user_data = null;
				}

				$notification_data[ $field->get_raw_name() ] = $field->sanitize( $user_data );

			}

			$notification_data = apply_filters( 'notification/notification/form/data/values', $notification_data, $ndata );

			update_post_meta( $this->get_post_id(), $this->notification_data_key . $notification->get_slug(), $notification_data );

			do_action( 'notification/notification/saved', $this->get_post_id(), $notification, $notification_data );

		}

	}

	/**
	 * Gets active notifications
	 *
	 * @return array
	 */
	public function get_active_notifications() {

		$active_notification_slugs = (array) $this->get_meta( $this->notification_enabled_key, false );

		$active_notifications = array();

		// translate slug to the object.
		foreach ( $active_notification_slugs as $slug ) {
			$notification = notification_get_single_notification( $slug );
			if ( ! empty( $notification ) ) {
				$active_notifications[] = clone $notification;
			}
		}

		return $active_notifications;

	}

	/**
	 * Gets active trigger
	 *
	 * @return mixed
	 */
	public function get_active_trigger() {
		return $this->get_meta( $this->active_trigger_key );
	}

	/**
	 * Saves active trigger
	 *
	 * @param string $trigger trigger slug.
	 * @return void
	 */
	public function save_active_trigger( $trigger ) {
		update_post_meta( $this->get_post_id(), $this->active_trigger_key, $trigger );
	}

	/**
	 * Gets CPT Notification for specific trigger
	 *
	 * @param  string $trigger_slug trigger slug.
	 * @return array                WP_Post array
	 */
	public function get_trigger_posts( $trigger_slug ) {

		$query_args = array(
			'numberposts' => -1,
			'post_type'   => 'notification',
			'meta_key'    => $this->active_trigger_key,
			'meta_value'  => $trigger_slug,
		);

		// WPML compat.
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$query_args['suppress_filters'] = 0;
		}

		return get_posts( $query_args );

	}

	/**
	 * Gets array of notifications with populated data for notification post
	 *
	 * @param  integer $post_id notification post ID.
	 * @return array            notification objects
	 */
	public function get_populated_notifications_for_post( $post_id ) {

		$this->set_post_id( $post_id );

		$notifications = $this->get_active_notifications();

		// set data for all active post notifications.
		foreach ( $notifications as $notification ) {
			$this->set_notification_data( $notification );
		}

		$this->clear_post_id();

		return $notifications;

	}

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
