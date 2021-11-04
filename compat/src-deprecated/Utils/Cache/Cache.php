<?php
/**
 * Cache object, which utilises key
 *
 * @package notification
 */

namespace BracketSpace\Notification\Utils\Cache;

/**
 * Cache class
 */
class Cache {

	/**
	 * Cache unique key
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Constructor
	 *
	 * @param string $key cache unique key.
	 */
	public function __construct( $key ) {
		notification_deprecated_class( __CLASS__, '8.0.2', 'BracketSpace\\Notification\\Dependencies\\Micropackage\\Cache\\Cache' );

		if ( empty( $key ) ) {
			trigger_error( 'Cache key cannot be empty' );
		}

		$this->key = $key;
	}

}
