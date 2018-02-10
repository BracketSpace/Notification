<?php
/**
 * User recipient
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Recipient;

use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

/**
 * User recipient
 */
class User extends Abstracts\Recipient {

	/**
	 * Recipient constructor
	 *
	 * @since [Next]
	 */
	public function __construct() {
		parent::__construct( array(
			'slug'          => 'user',
			'name'          => __( 'User' ),
			'default_value' => get_current_user_id(),
		) );
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
			$value = array( $this->get_default_value() );
		}

		$user = get_userdata( $value );

		if ( $user ) {
			return array( $user->user_email );
		}

		return array();

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
