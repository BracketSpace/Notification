<?php
/**
 * Role recipient
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Recipient;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;

/**
 * Role recipient
 */
class Role extends Abstracts\Recipient {

	/**
	 * Recipient constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		parent::__construct(
			array(
				'slug'          => 'role',
				'name'          => __( 'Role', 'notification' ),
				'default_value' => 'administrator',
			)
		);
	}

	/**
	 * Parses saved value something understood by notification
	 * Must be defined in the child class
	 *
	 * @param  string $value raw value saved by the user.
	 * @return array         array of resolved values
	 */
	public function parse_value( $value = '' ) {

		if ( empty( $value ) ) {
			$value = $this->get_default_value();
		}

		$users_query = new \WP_User_Query(
			array(
				'count_total' => true,
				'role'        => $value,
			)
		);

		$emails = array();

		foreach ( $users_query->get_results() as $user ) {
			$emails[] = $user->data->user_email;
		}

		return $emails;

	}

	/**
	 * Returns input object
	 *
	 * @return object
	 */
	public function input() {

		$roles = get_editable_roles();
		$opts  = array();

		foreach ( $roles as $role_slug => $role ) {

			$users_query = new \WP_User_Query(
				array(
					'count_total' => true,
					'role'        => $role_slug,
				)
			);

			$num_users = $users_query->get_total();
			// Translators: %s numer of users.
			$label = translate_user_role( $role['name'] ) . ' (' . sprintf( _n( '%s user', '%s users', $num_users, 'notification' ), $num_users ) . ')';

			$opts[ $role_slug ] = esc_html( $label );

		}

		return new Field\SelectField(
			array(
				'label'     => __( 'Recipient', 'notification' ),       // don't edit this!
				'name'      => 'recipient',       // don't edit this!
				'css_class' => 'recipient-value', // don't edit this!
				'value'     => $this->get_default_value(),
				'pretty'    => true,
				'options'   => $opts,
			)
		);

	}

}
