<?php
/**
 * User recipient
 */

namespace underDEV\Notification\Recipients\Core;

use underDEV\Notification\Notification\Recipient;

class User extends Recipient {

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
		$this->name = 'user';
	}

	/**
	 * Set description
	 */
	public function set_description() {
		$this->description = __( 'User', 'notification' );
	}

	/**
	 * Set default value
	 */
	public function set_default_value() {
		$this->default_value = 1;
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

		$user = get_userdata( $value );

		if ( $user ) {
			return $user->user_email;
		}

		return '';

	}

	/**
	 * Return input
	 * @return string input html
	 */
	public function input( $value = '', $id = 0 ) {

		$users = get_users();

		$html = '<select name="notification_recipient[' . $id . '][value]" class="widefat">';

			foreach ( $users as $user ) {
				$html .= '<option value="' . $user->ID . '" ' . selected( $value, $user->ID, false ) . '>' . $user->display_name . ' (' . $user->user_email . ')</option>';
			}

		$html .= '</select>';

		return $html;

	}

}
