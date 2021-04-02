<?php
/**
 * Deprecated Notification abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

/**
 * Deprecated Notification abstract class
 */
abstract class Notification extends Carrier {

	/**
	 * Notification constructor
	 *
	 * @param string $slug slug.
	 * @param string $name nice name.
	 */
	public function __construct( $slug, $name ) {
		notification_deprecated_class( __CLASS__, '6.0.0', 'BracketSpace\\Notification\\Abstracts\\Carrier' );
		parent::__construct( $slug, $name );
	}

}
