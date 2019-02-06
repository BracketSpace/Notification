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
	 * @param array $data Notification data.
	 */
	public function __construct( $data = [] ) {
		$this->setup( $data );
	}

	/**
	 * Getter and Setter methods
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
	 * Sets up Notification data from array.
	 *
	 * @since  [Next]
	 * @throws \Exception If wrong arguments has been passed.
	 * @param  array $data Data array.
	 * @return $this
	 */
	public function setup( $data = [] ) {

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
					$notifications[ $notification->get_slug() ] = $notification;
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

			foreach ( $data['extras'] as $key => $extra ) {
				if ( is_array( $extra ) || is_string( $extra ) || is_numeric( $extra ) ) {
					$extras[ $key ] = $extra;
				} else {
					throw new \Exception( 'Each extra must be an array or string or number.' );
				}
			}

			$this->set_extras( $extras );
		}

		// Version. If none provided, the current most recent version is used.
		$version = isset( $data['version'] ) && ! empty( $data['version'] ) ? $data['version'] : time();
		$this->set_version( $version );

		return $this;

	}

	/**
	 * Dumps the object to array
	 *
	 * @since  [Next]
	 * @return array
	 */
	public function to_array() {

		$notifications = [];

		foreach ( $this->get_notifications as $key => $notification ) {
			if ( $notification->enabled ) {
				$notifications[ $key ] = $notification->get_data();
			}
		}

		return [
			'hash'          => $this->get_hash(),
			'title'         => $this->get_title(),
			'trigger'       => $this->get_trigger()->get_slug(),
			'notifications' => $notifications,
			'enabled'       => $this->is_enabled(),
			'extras'        => $this->get_extras(),
			'version'       => $this->get_version(),
		];

	}

	/**
	 * Checks if enabled
	 * Alias for `get_enabled()` method
	 *
	 * @since  [Next]
	 * @return boolean
	 */
	public function is_enabled() {
		return (bool) $this->get_enabled();
	}

	/**
	 * Creates hash
	 *
	 * @since  [Next]
	 * @return string hash
	 */
	public static function create_hash() {
		return uniqid( 'notification_' );
	}

	/**
	 * Gets single Notification object
	 *
	 * @since  [Next]
	 * @param  string $notification_slug Notification slug.
	 * @return mixed                     Notification object or null.
	 */
	public function get_notification( $notification_slug ) {
		$notifications = $this->get_notifications();
		return isset( $notifications[ $notification_slug ] ) ? $notifications[ $notification_slug ] : null;
	}

	/**
	 * Add Notification to the set
	 *
	 * @since  [Next]
	 * @throws \Exception If you try to add already added notification.
	 * @param  mixed $notification Notification object or slug.
	 * @return Notification
	 */
	public function add_notification( $notification ) {

		if ( ! $notification instanceof Interfaces\Sendable ) {
			$notification = notification_get_single_notification( $notification );
		}

		$notifications = $this->get_notifications();

		if ( isset( $notifications[ $notifiction->get_slug() ] ) ) {
			throw new \Exception( sprintf( 'Notification %s already exists', $notification->get_name() ) );
		}

		$notifications[ $notifiction->get_slug() ] = $notification;
		$this->set_notifications( $notifications );

		return $notification;

	}

	/**
	 * Enables notification
	 *
	 * @since  [Next]
	 * @param  string $notification_slug Notification slug.
	 * @return void
	 */
	public function enable_notification( $notification_slug ) {

		$notification = $this->get_notification( $notification_slug );

		if ( null === $notification ) {
			$notification = $this->add_notification( $notification_slug );
		}

		$notification->enabled = true;

	}

	/**
	 * Disables notification
	 *
	 * @since  [Next]
	 * @param  string $notification_slug Notification slug.
	 * @return void
	 */
	public function disable_notification( $notification_slug ) {
		$notification = $this->get_notification( $notification_slug );
		if ( null !== $notification ) {
			$notification->enabled = false;
		}
	}

	/**
	 * Sets notification data
	 *
	 * @since  [Next]
	 * @param  string $notification_slug Notification slug.
	 * @param  array  $data              Notification data.
	 * @return void
	 */
	public function set_notification_data( $notification_slug, $data ) {
		$notification = $this->get_notification( $notification_slug );
		if ( null !== $notification ) {
			$notification->set_data( $data );
		}
	}

	/**
	 * Gets notification data
	 *
	 * @since  [Next]
	 * @param  string $notification_slug Notification slug.
	 * @return void
	 */
	public function get_notification_data( $notification_slug ) {
		$notification = $this->get_notification( $notification_slug );
		if ( null !== $notification ) {
			$notification->get_data( $data );
		}
	}

}
