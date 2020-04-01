<?php
/**
 * Receivable interface class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Interfaces;

use BracketSpace\Notification\Interfaces\Nameable;

/**
 * Receivable interface
 */
interface Receivable extends Nameable {

	/**
	 * Parses saved value something understood by notification
	 * Must be defined in the child class
	 *
	 * @param  string $value raw value saved by the user.
	 * @return array         array of resolved values
	 */
	public function parse_value( $value = ''  );

	/**
	 * Returns input object
	 * Must be defined in the child class
	 *
	 * @return object
	 */
	public function input();

	/**
	 * Gets default value
	 *
	 * @return string
	 */
	public function get_default_value();

}
