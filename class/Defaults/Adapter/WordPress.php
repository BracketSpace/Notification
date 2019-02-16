<?php
/**
 * WordPress Adapter class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Adapter;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Interfaces;

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
	 * Meta key for enabled Carriers
	 *
	 * @var string
	 */
	public static $metakey_carrier_enabled = '_enabled_notification';

	/**
	 * Meta key for Carrier data
	 *
	 * @var string
	 */
	public static $metakey_carrier_data = '_notification_type_';

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
			$this->set_post( $input );
		} elseif ( is_integer( $input ) ) {
			$this->set_post( get_post( $input ) );
		} else {
			throw new \Exception( 'Read method of WordPress adapter expects the post ID or WP_Post object' );
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

		// Carriers.
		$carrier_slug = (array) get_post_meta( $this->get_id(), self::$metakey_carrier_enabled, false );
		$carriers     = [];

		foreach ( $carrier_slug as $carrier_slug ) {
			$carrier = notification_get_single_notification( $carrier_slug );
			if ( ! empty( $carrier ) ) {
				$carrier_copy                     = clone $carrier;
				$carrier_copy->post_id            = $this->get_id();
				$carriers[ $carrier->get_slug() ] = $this->populate_notification( $carrier_copy );
			}
		}

		if ( ! empty( $carriers ) ) {
			$this->set_notifications( $carriers );
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
	 * @return $this || WP_Error
	 */
	public function save() {

		$data = $this->get_notification()->to_array();

		// WordPress post related: Title, Hash, Status, Version.
		$post_id = wp_insert_post( [
			'ID'          => $this->get_id(),
			'post_type'   => 'notification',
			'post_title'  => $data['title'],
			'post_name'   => $data['hash'],
			'post_status' => $data['enabled'] ? 'publish' : 'draft',
		], true );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		if ( ! $this->has_post() ) {
			$this->set_post( get_post( $post_id ) );
		}

		// Update version as WordPress automatically does this while updating the post.
		$this->set_version( time() );

		// Trigger.
		update_post_meta( $this->get_id(), self::$metakey_trigger, $data['trigger'] );

		// Carriers.
		// Loop through all defined to save the data of deactivated Carriers too.
		foreach ( $this->get_notification()->get_notifications() as $key => $carrier ) {

			if ( $carrier->enabled ) {
				add_post_meta( $this->get_id(), self::$metakey_carrier_enabled, $key );
			} else {
				delete_post_meta( $this->get_id(), self::$metakey_carrier_enabled, $key );
			}

			update_post_meta( $this->get_id(), self::$metakey_carrier_data . $key, $carrier->get_data() );

		}

		// Extras.
		// @todo Extras API #h1k0k. Presumabely the best to move to abstract adapter.
		$extras = false;

		return $this;

	}

	/**
	 * Checks if notification post has been just started
	 *
	 * @since [Next]
	 * @return boolean
	 */
	public function is_new() {
		return empty( $this->post ) || '0000-00-00 00:00:00' === $this->post->post_date_gmt;
	}

	/**
	 * Gets notification post ID
	 *
	 * @since [Next]
	 * @return integer post ID
	 */
	public function get_id() {
		return ! empty( $this->post ) ? $this->post->ID : 0;
	}

	/**
	 * Gets post
	 *
	 * @since [Next]
	 * @return null || WP_Post
	 */
	public function get_post() {
		return $this->post;
	}

	/**
	 * Sets post
	 *
	 * @since [Next]
	 * @param \WP_Post $post WP Post to set.
	 * @return $this
	 */
	public function set_post( \WP_Post $post ) {
		$this->post = $post;
		return $this;
	}

	/**
	 * Checks if adapter already have the post
	 *
	 * @since [Next]
	 * @return bool
	 */
	public function has_post() {
		return ! empty( $this->get_post() );
	}

	/**
	 * Populates Notification with field values
	 *
	 * @since  [Next]
	 * @throws \Exception If notification hasn't been found.
	 * @param  mixed $carrier Sendable object or Notification slug.
	 * @return Sendable
	 */
	public function populate_notification( $carrier ) {

		if ( ! $carrier instanceof Interfaces\Sendable ) {
			$carrier = notification_get_single_notification( $carrier );
		}

		if ( ! $carrier ) {
			throw new \Exception( 'Wrong Carroer slug' );
		}

		// Set enabled state.
		$enabled_carriers = (array) get_post_meta( $this->get_id(), self::$metakey_carrier_enabled, false );

		if ( in_array( $carrier->get_slug(), $enabled_carriers, true ) ) {
			$carrier->enabled = true;
		}

		// Set data.
		$data         = get_post_meta( $this->get_id(), self::$metakey_carrier_data . $carrier->get_slug(), true );
		$field_values = apply_filters_deprecated( 'notification/notification/form_fields/values', [ $data, $carrier ], '[Next]', 'notification/carrier/fields/values' );
		$field_values = apply_filters( 'notification/carrier/fields/values', $data, $carrier );

		foreach ( $carrier->get_form_fields() as $field ) {
			if ( isset( $field_values[ $field->get_raw_name() ] ) ) {
				$field->set_value( $field_values[ $field->get_raw_name() ] );
			}
		}

		return $carrier;

	}

}
