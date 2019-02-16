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

		// Carriers.
		if ( isset( $data['notifications'] ) ) {
			$carriers = [];

			foreach ( $data['notifications'] as $carrier ) {
				if ( $carrier instanceof Interfaces\Sendable ) {
					$carriers[ $carrier->get_slug() ] = $carrier;
				} else {
					throw new \Exception( 'Each Carrier object must implement Sendable interface' );
				}
			}

			$this->set_notifications( $carriers );
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
	 * Note: The notifications array contains only enabled notifications.
	 *
	 * @since  [Next]
	 * @return array
	 */
	public function to_array() {

		$carriers = [];

		foreach ( $this->get_notifications() as $key => $carrier ) {
			// Filter active only.
			if ( $carrier->enabled ) {
				$carriers[ $key ] = $carrier->get_data();
			}
		}

		$trigger = $this->get_trigger();

		return [
			'hash'          => $this->get_hash(),
			'title'         => $this->get_title(),
			'trigger'       => $trigger ? $trigger->get_slug() : '',
			'notifications' => $carriers,
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
	 * Gets single Carrier object
	 *
	 * @since  [Next]
	 * @param  string $carrier_slug Carrier slug.
	 * @return mixed                Carrier object or null.
	 */
	public function get_notification( $carrier_slug ) {
		$carriers = $this->get_notifications();
		return isset( $carriers[ $carrier_slug ] ) ? $carriers[ $carrier_slug ] : null;
	}

	/**
	 * Add Carrier to the set
	 *
	 * @since  [Next]
	 * @throws \Exception If you try to add already added Carrier.
	 * @throws \Exception If you try to add non-existing Carrier.
	 * @param  mixed $carrier Carrier object or slug.
	 * @return Carrier
	 */
	public function add_notification( $carrier ) {

		if ( ! $carrier instanceof Interfaces\Sendable ) {
			$carrier = notification_get_single_notification( $carrier );
		}

		if ( ! $carrier instanceof Interfaces\Sendable ) {
			throw new \Exception( 'Carrier hasn\'t been found' );
		}

		$carriers = $this->get_notifications();

		if ( isset( $carriers[ $carrier->get_slug() ] ) ) {
			throw new \Exception( sprintf( 'Carrier %s already exists', $carrier->get_name() ) );
		}

		$carriers[ $carrier->get_slug() ] = $carrier;
		$this->set_notifications( $carriers );

		return $carrier;

	}

	/**
	 * Enables Carrier
	 *
	 * @since  [Next]
	 * @param  string $carrier_slug Carrier slug.
	 * @return void
	 */
	public function enable_notification( $carrier_slug ) {

		$carrier = $this->get_notification( $carrier_slug );

		if ( null === $carrier ) {
			$carrier = $this->add_notification( $carrier_slug );
		}

		$carrier->enabled = true;

	}

	/**
	 * Disables Carrier
	 *
	 * @since  [Next]
	 * @param  string $carrier_slug Carrier slug.
	 * @return void
	 */
	public function disable_notification( $carrier_slug ) {
		$carrier = $this->get_notification( $carrier_slug );
		if ( null !== $carrier ) {
			$carrier->enabled = false;
		}
	}

	/**
	 * Sets Carriers
	 * Makes sure that the Notification slug is used as key.
	 *
	 * @since  [Next]
	 * @param  array $carriers Array of Carriers.
	 * @return void
	 */
	public function set_notifications( $carriers = [] ) {

		$saved_carriers = [];

		foreach ( $carriers as $carrier ) {
			$saved_carriers[ $carrier->get_slug() ] = $carrier;
		}

		$this->notifications = $saved_carriers;

	}

	/**
	 * Sets Carrier data
	 *
	 * @since  [Next]
	 * @param  string $carrier_slug Carrier slug.
	 * @param  array  $data         Carrier data.
	 * @return void
	 */
	public function set_notification_data( $carrier_slug, $data ) {
		$carrier = $this->get_notification( $carrier_slug );
		if ( null !== $carrier ) {
			$carrier->set_data( $data );
		}
	}

	/**
	 * Gets Carrier data
	 *
	 * @since  [Next]
	 * @param  string $carrier_slug Carrier slug.
	 * @return void
	 */
	public function get_notification_data( $carrier_slug ) {
		$carrier = $this->get_notification( $carrier_slug );
		if ( null !== $carrier ) {
			$carrier->get_data( $data );
		}
	}

}
