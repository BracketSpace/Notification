<?php
/**
 * Nonce field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Nonce field class
 */
class NonceField extends Field {

	/**
	 * Nonce key
	 *
	 * @var string
	 */
	protected $nonce_key = '';

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] ) {

		if ( ! isset( $params['nonce_key'] ) ) {
			trigger_error( 'NonceField requires nonce_key param', E_USER_ERROR );
		}

		$this->nonce_key = $params['nonce_key'];

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field() {
		return wp_nonce_field( $this->nonce_key, $this->get_name(), true, false );
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value ) {
		return null;
	}

}
