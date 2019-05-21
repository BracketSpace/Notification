<?php
/**
 * User registered trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User registered trigger class
 */
class UserRegistered extends UserTrigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'wordpress/user_registered', __( 'User registration', 'notification' ) );

		$this->add_action( 'user_register', 1000 );

		$this->set_description( __( 'Fires when user registers new account', 'notification' ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @param integer $user_id User ID.
	 * @return void
	 */
	public function action( $user_id ) {

		$this->user_id     = $user_id;
		$this->user_object = get_userdata( $this->user_id );
		$this->user_meta   = get_user_meta( $this->user_id );

		$this->user_registered_datetime = strtotime( $this->user_object->user_registered );

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\UrlTag( [
			'slug'        => 'user_password_setup_link',
			'name'        => __( 'User password setup link', 'notification' ),
			'description' => network_site_url( 'wp-login.php?action=rp&key=37f62f1363b04df4370753037853fe88&login=userlogin', 'login' ) . "\n" .
							__( 'After using this Merge Tag, no other password setup links will work.', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return network_site_url( 'wp-login.php?action=rp&key=' . $trigger->get_password_reset_key() . '&login=' . rawurlencode( $trigger->user_object->user_login ), 'login' );
			},
			'group'       => __( 'User', 'notification' ),
		] ) );

	}

	/**
	 * Gets password reset key
	 *
	 * @since  5.1.5
	 * @return string
	 */
	public function get_password_reset_key() {
		return get_password_reset_key( $this->user_object );
	}

}
