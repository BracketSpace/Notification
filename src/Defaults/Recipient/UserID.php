<?php
/**
 * User ID recipient
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Recipient;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;

/**
 * User ID recipient
 */
class UserID extends Abstracts\Recipient {

	/**
	 * Recipient constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		parent::__construct( [
			'slug'          => 'user_id',
			'name'          => __( 'User ID', 'notification' ),
			'default_value' => '',
		] );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param  string $value raw value saved by the user.
	 * @return array         array of resolved values
	 */
	public function parse_value( $value = '' ) {

		if ( empty( $value ) ) {
			return [];
		}

		$user_ids = array_map( 'trim', explode( ',', $value ) );
		$users    = get_users( [
			'include' => $user_ids,
			'fields'  => [ 'user_email' ],
		] );

		return wp_list_pluck( $users, 'user_email' );

	}

	/**
	 * {@inheritdoc}
	 *
	 * @return object
	 */
	public function input() {

		return new Field\InputField( [
			'label'       => __( 'Recipient', 'notification' ), // don't edit this!
			'name'        => 'recipient',                       // don't edit this!
			'css_class'   => 'recipient-value',                 // don't edit this!
			'placeholder' => __( '123 or {user_ID}', 'notification' ),
			'description' => __( 'You can use any valid email merge tag.', 'notification' ),
			'resolvable'  => true,
		] );

	}

}
