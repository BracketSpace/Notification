<?php
/**
 * Cache abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Utils\Interfaces;

/**
 * Cache abstract class
 */
abstract class Cache {

	/**
	 * Cache engine
	 *
	 * @var Interfaces\Cacheable
	 */
	protected $cache;

	/**
	 * Cache constructor
	 *
	 * @since [Next]
	 * @param Interfaces\Cacheable $cache Cache engine.
	 */
	public function __construct( Interfaces\Cacheable $cache ) {
		$this->cache = $cache;
	}

}
