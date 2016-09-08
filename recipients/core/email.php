<?php
/**
 * Custom email recipient
 */

namespace Notification\Recipients\Core;

use \Notification\Notification\Recipient;

class Email extends Recipient {

	/**
	 * Class constructor
	 */
	public function __construct() {

		parent::__construct();

	}

	/**
	 * Set name
	 */
	public function set_name() {
		$this->name = 'email';
	}

	/**
	 * Set description
	 */
	public function set_description() {
		$this->description = __( 'Email address', 'notification' );
	}

	/**
	 * Set default value
	 */
	public function set_default_value() {
		$this->default_value = '';
	}

	/**
	 * Parse value
	 * @param string  $value       saved value
	 * @param array   $tags_values parsed merge tags
	 * @return string              parsed value
	 */
	public function parse_value( $value = '', $tags_values = array() ) {
		
		if ( empty( $value ) ) {
			$value = $this->get_default_value();
		}

		return sanitize_email( $value );

	}

	/**
	 * Return input
	 * @return string input html
	 */
	public function input( $value = '', $id = 0 ) {

		$html = '<input type="email" class="widefat" name="notification_recipient[' . $id . '][value]" placeholder="' . __( 'email@domain.com', 'notification' ) . '" value="' . $value . '">';

		return $html;

	}

}
