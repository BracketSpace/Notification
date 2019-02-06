<?php
/**
 * WordPress Adapter class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Adapter;

use BracketSpace\Notification\Abstracts;

/**
 * WordPress Adapter class
 */
class WordPress extends Abstracts\Adapter {

	/**
	 * Notification post
	 *
	 * @var WP_Post
	 */
	protected $post;

	/**
	 * Meta key for enabled notifications
	 *
	 * @var string
	 */
	public static $metakey_notification_enabled = '_enabled_notification';

	/**
	 * Meta key for notification data
	 *
	 * @var string
	 */
	public static $metakey_notification_data = '_notification_type_';

	/**
	 * Meta key for active trigger
	 *
	 * @var string
	 */
	public static $metakey_trigger = '_trigger';

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Exception If wrong input param provided.
	 * @param mixed $input Input data.
	 * @return $this
	 */
	public function read( $input = null ) {

		if ( $input instanceof \WP_Post ) {
			$this->post = $input;
		} elseif ( is_integer( $input ) ) {
			$this->post = get_post( $input );
		} else {
			throw new \Exception( 'Read method of WordPress adapter expects the post ID or object' );
		}

		// Hash.
		$this->set_hash( $this->post->post_name );

		// Title.
		$this->set_title( $this->post->post_title );

		// Trigger.
		$trigger_slug = get_post_meta( $this->get_id(), self::$metakey_trigger, true );
		$trigger      = notification_get_single_trigger( $trigger_slug );

		if ( ! empty( $trigger ) ) {
			$this->set_trigger( $trigger );
		}

		// Notifications.
		$notification_slugs   = (array) get_post_meta( $this->get_id(), self::$metakey_notification_enabled, false );
		$notification_objects = [];

		foreach ( $notification_slugs as $notification_slug ) {
			$notification = notification_get_single_notification( $notification_slug );
			if ( ! empty( $notification ) ) {
				$notification_objects[ $notification->get_slug() ] = $this->populate_notification( clone $notification );
			}
		}

		if ( ! empty( $notification_objects ) ) {
			$this->set_notifications( $notification_objects );
		}

		// Status.
		$this->set_enabled( 'publish' === $this->post->post_status );

		// Extras.
		// @todo Extras API #h1k0k. Presumabely the best to move to abstract adapter.
		$extras = false;

		// Version.
		$this->set_version( strtotime( $this->post->post_modified_gmt ) );

		return $this;

	}

	/**
	 * {@inheritdoc}
	 *
	 * @return mixed
	 */
	public function save() {

		$data = $this->get_notification()->to_array();

		// WordPress post related: Title, Hash, Status, Version.
		wp_update_post( [
			'ID'          => $this->get_id(),
			'post_title'  => $data['title'],
			'post_name'   => $data['hash'],
			'post_status' => $data['enabled'] ? 'publish' : 'draft',
		], true );

		// Update version as WordPress automatically does this while updating the post.
		$this->set_version( time() );

		// Trigger.
		update_post_meta( $this->get_id(), self::$metakey_trigger, $data['trigger'] );

		// Notifications.
		delete_post_meta( $this->get_id(), self::$metakey_notification_enabled );

		foreach ( $data['notifications'] as $key => $notification_data ) {
			add_post_meta( $this->get_id(), self::$metakey_notification_enabled, $key );
			update_post_meta( $this->get_id(), self::$metakey_notification_data . $key, $notification_data );
		}

		// Extras.
		// @todo Extras API #h1k0k. Presumabely the best to move to abstract adapter.
		$extras = false;

	}

	/**
	 * Checks if notification post has been just started
	 *
	 * @return boolean
	 */
	public function is_new() {
		return '0000-00-00 00:00:00' === $this->post->post_date_gmt;
	}

	/**
	 * Gets notification post ID
	 *
	 * @return integer post ID
	 */
	public function get_id() {
		return $this->post->ID;
	}

	/**
	 * Populates Notification with field values
	 *
	 * @since  [Next]
	 * @throws \Exception If notification hasn't been found.
	 * @param  mixed $notification Sendable object or Notification slug.
	 * @return Sendable
	 */
	public function populate_notification( $notification ) {

		if ( ! $notification instanceof Interfaces\Sendable ) {
			$notification = notification_get_single_notification( $notification );
		}

		if ( ! $notification ) {
			throw new \Exception( 'Wrong notification slug' );
		}

		// Set enabled state.
		$enabled_notifications = (array) get_post_meta( $this->get_id(), self::$metakey_notification_enabled, false );

		// If this is new post, mark email notifiation as active for better UX.
		if ( $this->is_new() && $notification->get_slug() === 'email' ) {
			$enabled_notifications[] = 'email';
		}

		if ( in_array( $notification->get_slug(), $enabled_notifications, true ) ) {
			$notification->enabled = true;
		}

		$data         = get_post_meta( $this->get_id(), self::$metakey_notification_data . $notification_slug, true );
		$field_values = apply_filters( 'notification/notification/form_fields/values', $data, $notification );

		foreach ( $notification->get_form_fields() as $field ) {
			if ( isset( $field_values[ $field->get_raw_name() ] ) ) {
				$field->set_value( $field_values[ $field->get_raw_name() ] );
			}
		}

		return $notification;

	}

}
