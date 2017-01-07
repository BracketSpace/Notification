<?php
/**
 * Administrator recipient
 */

namespace Notification\Recipients\Core;

use \Notification\Notification\Recipient;

class Administrator extends Recipient {

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
		$this->name = 'administrator';
	}

	/**
	 * Set description
	 */
	public function set_description() {
		$this->description = __( 'Administrator', 'notification' );
	}

	/**
	 * Set default value
	 */
	public function set_default_value() {
		$this->default_value = get_option( 'admin_email' );
	}

	/**
	 * Parse value
	 * @param string  $value       saved value
	 * @param array   $tags_values parsed merge tags
	 * @return string              parsed value
	 */
	public function parse_value( $value = '', $tags_values = array(), $human_readable = false ) {

		if ( empty( $value ) ) {
			$value = $this->get_default_value();
		}

		return $value;

	}

	/**
	 * Return input
	 * @return string input html
	 */
	public function input( $value = '', $id = 0 ) {

		$html = '<input type="email" name="notification_recipient[' . $id . '][value]" class="widefat" value="' . $value . '" disabled="disabled">';

		return $html;

	}

}
