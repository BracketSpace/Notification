<?php
/**
 * User recipient
 */

namespace underDEV\Notification\Defaults\Recipient;
use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

class User extends Abstracts\Recipient {

	public function __construct() {
		parent::__construct( array(
			'slug'          => 'user',
			'name'          => __( 'User' ),
			'default_value' => get_current_user_id(),
		) );
	}

	/**
	 * Parses value
	 *
	 * @param string  $value saved value
	 * @return string        parsed value
	 */
	public function parse_value( $value = '' ) {

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
	 * Returns input object
	 *
	 * @return object
	 */
	public function input() {

		$users = get_users();
		$opts  = array();

		foreach ( $users as $user ) {
			$opts[ $user->ID ] = esc_html( $user->display_name ) . ' (' . $user->user_email . ')';
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
