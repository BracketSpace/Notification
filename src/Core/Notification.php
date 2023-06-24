<?php
/**
 * Notification class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Store;

/**
 * Notification class
 * Valid keys are:
 * - hash
 * - title
 * - trigger
 * - carriers
 * - enabled
 * - extras
 * - version
 *
 * @method string get_hash()
 * @method string get_title()
 * @method Interfaces\Triggerable|null get_trigger()
 * @method array<Interfaces\Sendable> get_carriers()
 * @method bool get_enabled()
 * @method array get_extras()
 * @method int get_version()
 * @method string get_source()
 * @method void set_hash( string $hash )
 * @method void set_title( string $title )
 * @method void set_trigger( Interfaces\Triggerable $trigger )
 * @method void set_enabled( bool $enabled )
 * @method void set_extras( array $extras )
 * @method void set_version( int $version )
 * @method void set_source( string $source )
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
	 * @var Interfaces\Triggerable|null
	 */
	protected $trigger;

	/**
	 * Carriers
	 *
	 * @var array
	 */
	protected $carriers = [];

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
	 * @var int
	 */
	protected $version;

	/**
	 * Source
	 *
	 * @var string
	 */
	protected $source = 'Internal';

	/**
	 * Constructor
	 *
	 * @since 6.0.0
	 * @param array $data Notification data.
	 */
	public function __construct( $data = [] ) {
		$this->setup( $data );
	}

	/**
	 * Getter and Setter methods
	 *
	 * @since  6.0.0
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
	 * Clone method
	 * Copies the Trigger and Carriers to new Carrier instance
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function __clone() {

		$trigger = $this->get_trigger();
		if ( ! empty( $trigger ) ) {
			$this->set_trigger( clone $trigger );
		}

		$carriers = [];
		foreach ( $this->get_carriers() as $key => $carrier ) {
			$carriers[ $key ] = clone $carrier;
		}
		$this->set_carriers( $carriers );

	}

	/**
	 * Sets up Notification data from array.
	 *
	 * @since  6.0.0
	 * @throws \Exception If wrong arguments has been passed.
	 * @param  array $data Data array.
	 * @return $this
	 */
	public function setup( $data = [] ) {

		// Hash. If not provided will be generated automatically.
		$hash = isset( $data['hash'] ) && ! empty( $data['hash'] ) ? $data['hash'] : self::create_hash();
		$this->set_hash( $hash );

		// Title.
		if ( isset( $data['title'] ) && ! empty( $data['title'] ) ) {
			$this->set_title( sanitize_text_field( $data['title'] ) );
		}

		// Trigger.
		if ( isset( $data['trigger'] ) && ! empty( $data['trigger'] ) ) {
			if ( $data['trigger'] instanceof Interfaces\Triggerable ) {
				$this->set_trigger( $data['trigger'] );
			} else {
				throw new \Exception( 'Trigger must implement Triggerable interface' );
			}
		}

		// Carriers.
		if ( isset( $data['carriers'] ) && ! empty( $data['carriers'] ) ) {
			$carriers = [];

			foreach ( $data['carriers'] as $carrier ) {
				if ( $carrier instanceof Interfaces\Sendable ) {
					$carriers[ $carrier->get_slug() ] = $carrier;
				} else {
					throw new \Exception( 'Each Carrier object must implement Sendable interface' );
				}
			}

			$this->set_carriers( $carriers );
		}

		// Status.
		if ( isset( $data['enabled'] ) ) {
			$this->set_enabled( (bool) $data['enabled'] );
		}

		// Extras.
		if ( isset( $data['extras'] ) ) {
			$extras = [];

			foreach ( $data['extras'] as $key => $extra ) {
				if ( is_array( $extra ) || is_string( $extra ) || is_numeric( $extra ) || is_bool( $extra ) ) {
					$extras[ $key ] = $extra;
				} else {
					throw new \Exception( 'Each extra must be an array or string or number or bool.' );
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
	 * @since  6.0.0
	 * @param  bool $only_enabled_carriers If only enabled Carriers should be saved.
	 * @return array
	 */
	public function to_array( $only_enabled_carriers = false ) {

		$carriers  = [];
		$_carriers = $only_enabled_carriers ? $this->get_enabled_carriers() : $this->get_carriers();
		foreach ( $_carriers as $carrier_slug => $carrier ) {
			$carriers[ $carrier_slug ] = $carrier->get_data();
		}

		$trigger = $this->get_trigger();

		return [
			'hash'     => $this->get_hash(),
			'title'    => $this->get_title(),
			'trigger'  => $trigger ? $trigger->get_slug() : '',
			'carriers' => $carriers,
			'enabled'  => $this->is_enabled(),
			'extras'   => $this->get_extras(),
			'version'  => $this->get_version(),
		];

	}

	/**
	 * Checks if enabled
	 * Alias for `get_enabled()` method
	 *
	 * @since  6.0.0
	 * @return boolean
	 */
	public function is_enabled() {
		return (bool) $this->get_enabled();
	}

	/**
	 * Creates hash
	 *
	 * @since  6.0.0
	 * @return string hash
	 */
	public static function create_hash() {
		return uniqid( 'notification_' );
	}

	/**
	 * Gets single Carrier object
	 *
	 * @since  6.0.0
	 * @param  string $carrier_slug Carrier slug.
	 * @return mixed                Carrier object or null.
	 */
	public function get_carrier( $carrier_slug ) {
		$carriers = $this->get_carriers();
		return isset( $carriers[ $carrier_slug ] ) ? $carriers[ $carrier_slug ] : null;
	}

	/**
	 * Gets enabled Carriers
	 *
	 * @since  6.0.0
	 * @return array
	 */
	public function get_enabled_carriers() {
		return array_filter( $this->get_carriers(), function ( $carrier ) {
			return $carrier->is_enabled();
		} );
	}

	/**
	 * Add Carrier to the set
	 *
	 * @since  6.0.0
	 * @throws \Exception If you try to add already added Carrier.
	 * @throws \Exception If you try to add non-existing Carrier.
	 * @param  Interfaces\Sendable|string $carrier Carrier object or slug.
	 * @return Interfaces\Sendable
	 */
	public function add_carrier( $carrier ) {

		if ( ! $carrier instanceof Interfaces\Sendable ) {
			$carrier = Store\Carrier::get( $carrier );
		}

		if ( ! $carrier instanceof Interfaces\Sendable ) {
			throw new \Exception( 'Carrier hasn\'t been found' );
		}

		$carriers = $this->get_carriers();

		if ( isset( $carriers[ $carrier->get_slug() ] ) ) {
			throw new \Exception( sprintf( 'Carrier %s already exists', $carrier->get_name() ) );
		}

		$carriers[ $carrier->get_slug() ] = $carrier;
		$this->set_carriers( $carriers );

		return $carrier;

	}

	/**
	 * Enables Carrier
	 *
	 * @since  6.0.0
	 * @param  string $carrier_slug Carrier slug.
	 * @return void
	 */
	public function enable_carrier( $carrier_slug ) {

		$carrier = $this->get_carrier( $carrier_slug );

		if ( null === $carrier ) {
			$carrier = $this->add_carrier( $carrier_slug );
		}

		$carrier->enable();

	}

	/**
	 * Disables Carrier
	 *
	 * @since  6.0.0
	 * @param  string $carrier_slug Carrier slug.
	 * @return void
	 */
	public function disable_carrier( $carrier_slug ) {
		$carrier = $this->get_carrier( $carrier_slug );
		if ( null !== $carrier ) {
			$carrier->disable();
		}
	}

	/**
	 * Sets Carriers
	 * Makes sure that the Notification slug is used as key.
	 *
	 * @since  6.0.0
	 * @param  array $carriers Array of Carriers.
	 * @return void
	 */
	public function set_carriers( $carriers = [] ) {

		$saved_carriers = [];

		foreach ( $carriers as $carrier ) {
			$saved_carriers[ $carrier->get_slug() ] = $carrier;
		}

		$this->carriers = $saved_carriers;

	}

	/**
	 * Sets Carrier data
	 *
	 * @since  6.0.0
	 * @param  string $carrier_slug Carrier slug.
	 * @param  array  $data         Carrier data.
	 * @return void
	 */
	public function set_carrier_data( $carrier_slug, $data ) {
		$carrier = $this->get_carrier( $carrier_slug );
		if ( null !== $carrier ) {
			$carrier->set_data( $data );
		}
	}

	/**
	 * Gets Carrier data
	 *
	 * @since  6.0.0
	 * @param  string $carrier_slug Carrier slug.
	 * @return void
	 */
	public function get_carrier_data( $carrier_slug ) {
		$carrier = $this->get_carrier( $carrier_slug );
		if ( null !== $carrier ) {
			$carrier->get_data();
		}
	}

	/**
	 * Gets single extra data value.
	 *
	 * @since  6.0.0
	 * @param  string $key Extra data key.
	 * @return mixed       Extra data value or null
	 */
	public function get_extra( $key ) {
		$extras = $this->get_extras();
		return isset( $extras[ $key ] ) ? $extras[ $key ] : null;
	}

	/**
	 * Removes single extra data.
	 *
	 * @since  6.0.0
	 * @param  string $key Extra data key.
	 * @return void
	 */
	public function remove_extra( $key ) {

		$extras = $this->get_extras();

		if ( isset( $extras[ $key ] ) ) {
			unset( $extras[ $key ] );
		}

		$this->set_extras( $extras );

	}

	/**
	 * Add extra data
	 *
	 * @since  6.0.0
	 * @throws \Exception If extra is not type of array, string or number or boolean.
	 * @param  string $key   Extra data key.
	 * @param  mixed  $value Extra data value.
	 * @return $this
	 */
	public function add_extra( $key, $value ) {

		if ( ! is_array( $value ) && ! is_string( $value ) && ! is_numeric( $value ) && ! is_bool( $value ) ) {
			throw new \Exception( 'Extra data must be an array or string or number.' );
		}

		$extras = $this->get_extras();

		// Create or update key.
		$extras[ $key ] = $value;

		$this->set_extras( $extras );

		return $this;

	}

	/**
	 * Refreshes the hash
	 *
	 * @since  6.1.4
	 * @return $this
	 */
	public function refresh_hash() {
		$this->set_hash( self::create_hash() );
		return $this;
	}

}
