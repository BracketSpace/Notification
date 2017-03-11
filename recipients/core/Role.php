<?php
/**
 * Role recipient
 */

namespace underDEV\Notification\Recipients\Core;

use underDEV\Notification\Notification\Recipient;

class Role extends Recipient {

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
		$this->name = 'role';
	}

	/**
	 * Set description
	 */
	public function set_description() {
		$this->description = __( 'Role', 'notification' );
	}

	/**
	 * Set default value
	 */
	public function set_default_value() {
		$this->default_value = 'administrator';
	}

	/**
	 * Parse value
	 * @param string  $value          saved value
	 * @param array   $tags_values    parsed merge tags
	 * @param boolean $human_readable false if should return emails or true if descriptive string
	 * @return mixed                  string for human or array with emails
	 */
	public function parse_value( $value = '', $tags_values = array(), $human_readable = false ) {

		if ( empty( $value ) ) {
			$value = $this->get_default_value();
		}

		$users_query = new \WP_User_Query( array(
			'count_total' => true,
			'role' => $value
		) );

		if ( $human_readable ) {

			$num_users = $users_query->get_total();
			$role = get_role( $value );
			return translate_user_role( ucfirst( $role->name ) ) . ' (' . sprintf( _n( '1 user', '%s users', $num_users, 'notification' ), $num_users ). ')';

		} else {

			$emails = array();

			foreach ( $users_query->get_results() as $user ) {
				$emails[] = $user->data->user_email;
			}

			return $emails;

		}

	}

	/**
	 * Return input
	 * @return string input html
	 */
	public function input( $value = '', $id = 0 ) {

		$roles = get_editable_roles();

		$html = '<select name="notification_recipient[' . $id . '][value]" class="widefat">';

			foreach ( $roles as $role_slug => $role ) {

				$users_query = new \WP_User_Query( array(
					'count_total' => true,
					'role' => $role_slug
				) );

				$num_users = $users_query->get_total();
				$label = translate_user_role( $role['name'] ) . ' (' . sprintf( _n( '1 user', '%s users', $num_users, 'notification' ), $num_users );

				$html .= '<option value="' . $role_slug . '" ' . selected( $value, $role_slug, false ) . '>' . $label . ')</option>';
			}

		$html .= '</select>';

		return $html;

	}

}
