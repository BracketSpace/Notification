<?php
/**
 * Recipient abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Traits;

/**
 * Recipient abstract class
 */
abstract class Recipient implements Interfaces\Receivable {

	use Traits\ClassUtils, Traits\HasName, Traits\HasSlug;

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

		if ( ! empty( $params['slug'] ) ) {
			$this->set_slug( $params['slug'] );
		}

		if ( ! empty( $params['name'] ) ) {
			$this->set_name( $params['name'] );
		}

		if ( ! isset( $params['default_value'] ) ) {
			trigger_error( 'Recipient requires default_value', E_USER_ERROR );
		}

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
	 * @return Interfaces\Fillable
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
