<?php
/**
 * Notification class
 *
 * @package notification
 */

declare(strict_types=1);

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
	 * @param  string $methodName Method name.
	 * @param  array  $arguments   Arguments.
	 * @return mixed
	 */
	public function __call( $methodName, $arguments ) {

		// Getter.
		if ( 0 === strpos( $methodName, 'get_' ) ) {
			$property = str_replace( 'get_', '', $methodName );

			if ( property_exists( $this, $property ) ) {
				return $this->$property;
			} else {
				throw new \Exception( sprintf( 'Property %s doesn\'t exists.', $property ) );
			}
		}

		// Setter.
		if ( 0 === strpos( $methodName, 'set_' ) ) {
			$property = str_replace( 'set_', '', $methodName );

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

		$trigger = $this->getTrigger();
		if ( ! empty( $trigger ) ) {
			$this->setTrigger( clone $trigger );
		}

		$carriers = [];
		foreach ( $this->getCarriers() as $key => $carrier ) {
			$carriers[ $key ] = clone $carrier;
		}
		$this->setCarriers( $carriers );

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
		$this->setHash( $hash );

		// Title.
		if ( isset( $data['title'] ) && ! empty( $data['title'] ) ) {
			$this->setTitle( sanitize_text_field( $data['title'] ) );
		}

		// Trigger.
		if ( isset( $data['trigger'] ) && ! empty( $data['trigger'] ) ) {
			if ( $data['trigger'] instanceof Interfaces\Triggerable ) {
				$this->setTrigger( $data['trigger'] );
			} else {
				throw new \Exception( 'Trigger must implement Triggerable interface' );
			}
		}

		// Carriers.
		if ( isset( $data['carriers'] ) && ! empty( $data['carriers'] ) ) {
			$carriers = [];

			foreach ( $data['carriers'] as $carrier ) {
				if ( $carrier instanceof Interfaces\Sendable ) {
					$carriers[ $carrier->getSlug() ] = $carrier;
				} else {
					throw new \Exception( 'Each Carrier object must implement Sendable interface' );
				}
			}

			$this->setCarriers( $carriers );
		}

		// Status.
		if ( isset( $data['enabled'] ) ) {
			$this->setEnabled( (bool) $data['enabled'] );
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

			$this->setExtras( $extras );
		}

		// Version. If none provided, the current most recent version is used.
		$version = isset( $data['version'] ) && ! empty( $data['version'] ) ? $data['version'] : time();
		$this->setVersion( $version );

		return $this;

	}

	/**
	 * Dumps the object to array
	 *
	 * @since  6.0.0
	 * @param  bool $onlyEnabledCarriers If only enabled Carriers should be saved.
	 * @return array
	 */
	public function toArray( $onlyEnabledCarriers = false ) {

		$carriers  = [];
		$_carriers = $onlyEnabledCarriers ? $this->getEnabledCarriers() : $this->getCarriers();
		foreach ( $_carriers as $carrierSlug => $carrier ) {
			$carriers[ $carrierSlug ] = $carrier->getData();
		}

		$trigger = $this->getTrigger();

		return [
			'hash'     => $this->getHash(),
			'title'    => $this->getTitle(),
			'trigger'  => $trigger ? $trigger->getSlug() : '',
			'carriers' => $carriers,
			'enabled'  => $this->isEnabled(),
			'extras'   => $this->getExtras(),
			'version'  => $this->getVersion(),
		];

	}

	/**
	 * Checks if enabled
	 * Alias for `get_enabled()` method
	 *
	 * @since  6.0.0
	 * @return boolean
	 */
	public function isEnabled() {
		return (bool) $this->getEnabled();
	}

	/**
	 * Creates hash
	 *
	 * @since  6.0.0
	 * @return string hash
	 */
	public static function createHash() {
		return uniqid( 'notification_' );
	}

	/**
	 * Gets single Carrier object
	 *
	 * @since  6.0.0
	 * @param  string $carrierSlug Carrier slug.
	 * @return mixed                Carrier object or null.
	 */
	public function getCarrier( $carrierSlug ) {
		$carriers = $this->getCarriers();
		return isset( $carriers[ $carrierSlug ] ) ? $carriers[ $carrierSlug ] : null;
	}

	/**
	 * Gets enabled Carriers
	 *
	 * @since  6.0.0
	 * @return array
	 */
	public function getEnabledCarriers() {
		return array_filter( $this->getCarriers(), function ( $carrier ) {
			return $carrier->isEnabled();
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
	public function addCarrier( $carrier ) {

		if ( ! $carrier instanceof Interfaces\Sendable ) {
			$carrier = Store\Carrier::get( $carrier );
		}

		if ( ! $carrier instanceof Interfaces\Sendable ) {
			throw new \Exception( 'Carrier hasn\'t been found' );
		}

		$carriers = $this->getCarriers();

		if ( isset( $carriers[ $carrier->getSlug() ] ) ) {
			throw new \Exception( sprintf( 'Carrier %s already exists', $carrier->getName() ) );
		}

		$carriers[ $carrier->getSlug() ] = $carrier;
		$this->setCarriers( $carriers );

		return $carrier;

	}

	/**
	 * Enables Carrier
	 *
	 * @since  6.0.0
	 * @param  string $carrierSlug Carrier slug.
	 * @return void
	 */
	public function enableCarrier( $carrierSlug ) {

		$carrier = $this->getCarrier( $carrierSlug );

		if ( null === $carrier ) {
			$carrier = $this->addCarrier( $carrierSlug );
		}

		$carrier->enable();

	}

	/**
	 * Disables Carrier
	 *
	 * @since  6.0.0
	 * @param  string $carrierSlug Carrier slug.
	 * @return void
	 */
	public function disableCarrier( $carrierSlug ) {
		$carrier = $this->getCarrier( $carrierSlug );
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
	public function setCarriers( $carriers = [] ) {

		$savedCarriers = [];

		foreach ( $carriers as $carrier ) {
			$savedCarriers[ $carrier->getSlug() ] = $carrier;
		}

		$this->carriers = $savedCarriers;

	}

	/**
	 * Sets Carrier data
	 *
	 * @since  6.0.0
	 * @param  string $carrierSlug Carrier slug.
	 * @param  array  $data         Carrier data.
	 * @return void
	 */
	public function setCarrierData( $carrierSlug, $data ) {
		$carrier = $this->getCarrier( $carrierSlug );
		if ( null !== $carrier ) {
			$carrier->setData( $data );
		}
	}

	/**
	 * Gets Carrier data
	 *
	 * @since  6.0.0
	 * @param  string $carrierSlug Carrier slug.
	 * @return void
	 */
	public function getCarrierData( $carrierSlug ) {
		$carrier = $this->getCarrier( $carrierSlug );
		if ( null !== $carrier ) {
			$carrier->getData();
		}
	}

	/**
	 * Gets single extra data value.
	 *
	 * @since  6.0.0
	 * @param  string $key Extra data key.
	 * @return mixed       Extra data value or null
	 */
	public function getExtra( $key ) {
		$extras = $this->getExtras();
		return isset( $extras[ $key ] ) ? $extras[ $key ] : null;
	}

	/**
	 * Removes single extra data.
	 *
	 * @since  6.0.0
	 * @param  string $key Extra data key.
	 * @return void
	 */
	public function removeExtra( $key ) {

		$extras = $this->getExtras();

		if ( isset( $extras[ $key ] ) ) {
			unset( $extras[ $key ] );
		}

		$this->setExtras( $extras );

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
	public function addExtra( $key, $value ) {

		if ( ! is_array( $value ) && ! is_string( $value ) && ! is_numeric( $value ) && ! is_bool( $value ) ) {
			throw new \Exception( 'Extra data must be an array or string or number.' );
		}

		$extras = $this->getExtras();

		// Create or update key.
		$extras[ $key ] = $value;

		$this->setExtras( $extras );

		return $this;

	}

	/**
	 * Refreshes the hash
	 *
	 * @since  6.1.4
	 * @return $this
	 */
	public function refreshHash() {
		$this->setHash( self::create_hash() );
		return $this;
	}

}
