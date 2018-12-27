<?php
/**
 * Notification class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

/**
 * Notification class
 */
class Notification {

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
	protected $metakey_notification_enabled = '_enabled_notification';

	/**
	 * Meta key for notification data
	 *
	 * @var string
	 */
	protected $metakey_notification_data = '_notification_type_';

	/**
	 * Meta key for active trigger
	 *
	 * @var string
	 */
	protected $metakey_trigger = '_trigger';

	/**
	 * Constructor
	 *
	 * @since [Next]
	 * @throws \Exception If param is not int nor WP_Post.
	 * @param mixed $post WP_Post || Post ID.
	 */
	public function __construct( $post ) {

		if ( $post instanceof \WP_Post ) {
			$this->post = $post;
		} elseif ( is_integer( $post ) ) {
			$this->post = get_post( $post );
		} else {
			throw new \Exception( 'You must provide an WP_Post or Post ID.' );
		}

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
	 * Gets notification post title
	 *
	 * @return string post title
	 */
	public function get_title() {
		return $this->post->post_title;
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
	 * Sets trigger
	 *
	 * @since [Next]
	 * @param string $trigger_slug Trigger slug.
	 */
	public function set_trigger( $trigger_slug ) {
		update_post_meta( $this->get_id(), $this->metakey_trigger, sanitize_text_field( $trigger_slug ) );
	}

	/**
	 * Gets trigger
	 *
	 * @since [Next]
	 * @return mixed string or false
	 */
	public function get_trigger() {
		return get_post_meta( $this->get_id(), $this->metakey_trigger, true );
	}

	/**
	 * Enables notification and sets its data
	 *
	 * @since  [Next]
	 * @param  string $notification_slug Notification slug.
	 * @param  mixed  $notification_data Notification data or false if none.
	 * @return void
	 */
	public function enable_notification( $notification_slug, $notification_data = false ) {

		// Check if notification hasn't been enabled yet.
		$active_notifications = $this->get_notifications( 'slugs' );
		if ( ! in_array( $notification_slug, $active_notifications, true ) ) {
			add_post_meta( $this->get_id(), $this->metakey_notification_enabled, $notification_slug );
		}

		if ( $notification_data ) {
			$this->set_notification_data( $notification_slug, $notification_data );
		}

	}

	/**
	 * Disable notification
	 *
	 * @since  [Next]
	 * @param  string  $notification_slug Notification slug.
	 * @param  boolean $delete_data       If Notification data should be deleted.
	 * @return void
	 */
	public function disable_notification( $notification_slug, $delete_data = false ) {

		delete_post_meta( $this->get_id(), $this->metakey_notification_enabled, $notification_slug );

		if ( $delete_data ) {
			delete_post_meta( $this->get_id(), $this->metakey_notification_data . $notification_slug );
		}

	}

	/**
	 * Sets notification data
	 *
	 * @since  [Next]
	 * @param  string $notification_slug     Notification slug.
	 * @param  array  $raw_notification_data Notification raw data.
	 * @return void
	 */
	public function set_notification_data( $notification_slug, $raw_notification_data ) {

		$notification = notification_get_single_notification( $notification_slug );

		if ( ! $notification ) {
			return;
		}

		$notification_data = array();

		// Sanitize each field individually.
		foreach ( $notification->get_form_fields() as $field ) {

			if ( isset( $raw_notification_data[ $field->get_raw_name() ] ) ) {
				$user_data = $raw_notification_data[ $field->get_raw_name() ];
			} else {
				$user_data = null;
			}

			$notification_data[ $field->get_raw_name() ] = $field->sanitize( $user_data );

		}

		$notification_data = apply_filters( 'notification/notification/form/data/values', $notification_data, $raw_notification_data );

		update_post_meta( $this->get_id(), $this->metakey_notification_data . $notification_slug, $notification_data );

	}

	/**
	 * Gets notification data
	 *
	 * @since  [Next]
	 * @param  string $notification_slug Notification slug.
	 * @return array
	 */
	public function get_notification_data( $notification_slug ) {
		$data = get_post_meta( $this->get_id(), $this->metakey_notification_data . $notification_slug, true );
		return apply_filters( 'notification/notification/form_fields/values', $data, notification_get_single_notification( $notification_slug ) );
	}

	/**
	 * Gets enabled notifications
	 *
	 * @since  [Next]
	 * @param  string  $type     Type to return: objects || slugs.
	 * @param  boolean $populate If Notification objects should be populated with data.
	 * @return array
	 */
	public function get_notifications( $type = 'objects', $populate = false ) {

		$slugs = (array) get_post_meta( $this->get_id(), $this->metakey_notification_enabled, false );

		if ( 'slugs' === $type ) {
			return $slugs;
		}

		$objects = array();

		// Translate slug to the object.
		foreach ( $slugs as $slug ) {
			$notification = notification_get_single_notification( $slug );
			if ( ! empty( $notification ) ) {

				$cloned_notification = clone $notification;

				if ( $populate ) {
					$this->populate_notification( $cloned_notification );
				}

				$objects[] = $cloned_notification;

			}
		}

		return $objects;

	}

	/**
	 * Popupates Notification with field values
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

		$field_values = get_notification_data( $notification->get_slug() );

		foreach ( $notification->get_form_fields() as $field ) {
			if ( isset( $field_values[ $field->get_raw_name() ] ) ) {
				$field->set_value( $field_values[ $field->get_raw_name() ] );
			}
		}

		return $notification;

	}

}
