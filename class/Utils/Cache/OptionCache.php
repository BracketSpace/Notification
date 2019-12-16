<?php
/**
 * Option Cache
 *
 * @uses    options table
 * @uses    Cacheable Interface
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Cache;

use BracketSpace\Notification\Utils\Cache\Cache;
use BracketSpace\Notification\Utils\Interfaces\Cacheable;

/**
 * Option cache
 */
class OptionCache extends Cache implements Cacheable {

	/**
	 * Cache group
	 *
	 * @var string
	 */
	protected $group = '_notification_cache';

	/**
	 * Constructor
	 *
	 * @param string $key   cache unique key.
	 * @param string $group cache group, optional.
	 */
	public function __construct( $key, $group = null ) {

		parent::__construct( $key );

		if ( null !== $group ) {
			$this->group = $group;
		}

	}

	/**
	 * Sets cache value
	 *
	 * @param mixed $value value to store.
	 * @return object $this
	 */
	public function set( $value ) {
		update_option( $this->group . $this->key, $value );
		return $this;
	}

	/**
	 * Adds cache if it's not already set
	 *
	 * @param mixed $value value to store.
	 * @return object $this
	 */
	public function add( $value ) {
		add_option( $this->group . $this->key, $value );
		return $this;
	}

	/**
	 * Gets value from cache
	 *
	 * @param  boolean $force not used, transients are always get from storage.
	 * @return mixed          cached value
	 */
	public function get( $force = false ) {
		return get_option( $this->group . $this->key, $value );
	}

	/**
	 * Deletes value from cache
	 *
	 * @return object $this
	 */
	public function delete() {
		delete_option( $this->group . $this->key );
		return $this;
	}

}
