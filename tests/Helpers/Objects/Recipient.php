<?php
/**
 * Carrier class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Abstracts\Recipient as AbstractRecipient;

/**
 * Recipient class
 */
class Recipient extends AbstractRecipient {

	/**
	 * Recipient input default value
	 *
	 * @var string
	 */
	protected $default_value;

	/**
	 * Recipient constructor
	 *
	 * @since 5.0.0
	 * @param array $params recipient configuration params.
	 */
	public function __construct( $params = [] ) {
		parent::__construct( $params );
	}

	/**
	 * Parses saved value something understood by the Carrier
	 *
	 * @param  string $value raw value saved by the user.
	 * @return array         array of resolved values
	 */
	public function parse_value( $value = '' ){
		return;
	}

	/**
	 * Returns input object
	 *
	 * @return object
	 */
	public function input(){
		return;
	}

	/**
	 * Gets default value
	 *
	 * @return string
	 */
	public function get_default_value() {
		return $this->default_value;
	}

}
