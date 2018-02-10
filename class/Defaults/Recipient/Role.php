<?php
/**
 * Role recipient
 */

namespace underDEV\Notification\Defaults\Recipient;

use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

class Role extends Abstracts\Recipient {

	public function __construct() {
		parent::__construct( array(
			'slug'          => 'role',
			'name'          => __( 'Role' ),
			'default_value' => 'administrator',
		) );
	}

	/**
	 * Parses value
	 *
	 * @param string $value saved value
	 * @return string        parsed value
	 */
	public function parse_value( $value = '' ) {

		if ( empty( $value ) ) {
			$value = $this->get_default_value();
		}

		$users_query = new \WP_User_Query( array(
			'count_total' => true,
			'role'        => $value
		) );

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

			$users_query = new \WP_User_Query( array(
				'count_total' => true,
				'role' => $role_slug
			) );

			$num_users = $users_query->get_total();
			$label = translate_user_role( $role['name'] ) . ' (' . sprintf( _n( '1 user', '%s users', $num_users, 'notification' ), $num_users ) . ')';

			$opts[ $role_slug ] = esc_html( $label );

		}

		return new Field\SelectField( array(
			'label'     => 'Recipient',       // don't edit this!
			'name'      => 'recipient',       // don't edit this!
			'css_class' => 'recipient-value', // don't edit this!
			'value'     => $this->get_default_value(),
			'pretty'    => true,
			'options'   => $opts
		) );

	}

}
