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
	 * @return void
	 */
	public function parse_value( $value = '' ){
	}

	/**
	 * Returns input object
	 *
	 * @return void
	 */
	public function input(){

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
