<?php
/**
 * Recipient abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;

/**
 * Recipient abstract class
 */
abstract class Recipient extends Common implements Interfaces\Receivable {

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

		if ( ! isset( $params['slug'], $params['name'], $params['default_value'] ) ) {
			trigger_error( 'Recipient requires slug, name and default_value', E_USER_ERROR );
		}

		$this->slug          = $params['slug'];
		$this->name          = $params['name'];
		$this->default_value = $params['default_value'];

	}

	/**
	 * Parses saved value something understood by the Carrier
	 *
	 * @param  string $value raw value saved by the user.
	 * @return array         array of resolved values
	 */
	abstract public function parse_value( $value = '' );

	/**
	 * Returns input object
	 *
	 * @return object
	 */
	abstract public function input();

	/**
	 * Gets default value
	 *
	 * @return string
	 */
	public function get_default_value() {
		return $this->default_value;
	}

}
