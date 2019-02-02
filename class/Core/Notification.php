<?php
/**
 * Notification class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Interfaces;

/**
 * Notification class
 * Valid keys are:
 * - hash
 * - title
 * - trigger
 * - notifications
 * - enabled
 * - extras
 * - version
 */
class Notification {

	/**
	 * Hash
	 *
	 * @var string
	 */
	protected $hash;

	/**
	 * Title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Trigger
	 *
	 * @var Interfaces\Triggerable
	 */
	protected $trigger;

	/**
	 * Notifications
	 *
	 * @var array
	 */
	protected $notifications = [];

	/**
	 * Status
	 *
	 * @var bool
	 */
	protected $enabled = true;

	/**
	 * Extras
	 *
	 * @var array
	 */
	protected $extras = [];

	/**
	 * Version
	 *
	 * @var integer
	 */
	protected $version;

	/**
	 * Constructor
	 *
	 * @since [Next]
	 * @throws \Exception If wrong arguments has been passed.
	 * @param array $data Notification data.
	 */
	public function __construct( $data ) {

		// Hash. If not provided will be generated automatically.
		$hash = isset( $data['hash'] ) && ! empty( $data['hash'] ) ? $data['hash'] : self::create_hash();
		$this->set_hash( $hash );

		// Title.
		if ( isset( $data['title'] ) ) {
			$this->set_title( sanitize_text_field( $data['title'] ) );
		}

		// Trigger.
		if ( isset( $data['trigger'] ) ) {
			if ( $data['trigger'] instanceof Interfaces\Triggerable ) {
				$this->set_trigger( $data['trigger'] );
			} else {
				throw new \Exception( 'Trigger must implement Triggerable interface' );
			}
		}

		// Notifications.
		if ( isset( $data['notifications'] ) ) {
			$notifications = [];

			foreach ( $data['notifications'] as $notification ) {
				if ( $notification instanceof Interfaces\Sendable ) {
					$notifications[] = $notification;
				} else {
					throw new \Exception( 'Each Notifiation object must implement Sendable interface' );
				}
			}

			$this->set_notifications( $notifications );
		}

		// Status.
		if ( isset( $data['enabled'] ) ) {
			$this->set_enabled( (bool) $data['enabled'] );
		}

		// Extras.
		if ( isset( $data['extras'] ) ) {
			$extras = [];

			foreach ( $data['extras'] as $extra ) {
				if ( is_array( $extra ) || is_string( $extra ) || is_numeric( $extra ) ) {
					$extras[] = $extra;
				} else {
					throw new \Exception( 'Each extra must be an array or string or number.' );
				}
			}

			$this->set_extras( $extras );
		}

		// Version. If none provided, the current most recent version is used.
		$version = isset( $data['version'] ) && ! empty( $data['version'] ) ? $data['version'] : time();
		$this->set_version( $version );

	}

	/**
	 * Getter method
	 *
	 * @since  [Next]
	 * @throws \Exception If no property has been found.
	 * @param  string $method_name Method name.
	 * @param  array  $arguments   Arguments.
	 * @return mixed
	 */
	public function __call( $method_name, $arguments ) {

		// Getter.
		if ( 0 === strpos( $method_name, 'get_' ) ) {
			$property = str_replace( 'get_', '', $method_name );

			if ( property_exists( $this, $property ) ) {
				return $this->$property;
			} else {
				throw new \Exception( sprintf( 'Property %s doesn\'t exists.', $property ) );
			}
		}

		// Setter.
		if ( 0 === strpos( $method_name, 'set_' ) ) {
			$property = str_replace( 'set_', '', $method_name );

			if ( isset( $arguments[0] ) ) {
				$this->$property = $arguments[0];
			} else {
				throw new \Exception( 'You must provide the property value' );
			}
		}

	}

	/**
	 * Creates hash
	 *
	 * @return string hash
	 */
	public static function create_hash() {
		return uniqid( 'notification_' );
	}

}
