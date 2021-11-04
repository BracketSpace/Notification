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
	 * User meta data
	 *
	 * @var array
	 */
	public $user_meta;

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'user/registered', __( 'User registration', 'notification' ) );

		$this->add_action( 'user_register', 1000 );

		$this->set_description( __( 'Fires when user registers new account', 'notification' ) );

	}

	/**
	 * Sets trigger's context
	 *
	 * @param integer $user_id User ID.
	 * @return void
	 */
	public function context( $user_id ) {

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
			'resolver'    => function ( $trigger ) {
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

		add_filter( 'allow_password_reset', '__return_true', 999999999 );
		add_filter( 'notification/trigger/wordpress/user_password_reset_request/bail_for_registration', '__return_true', 999999999 );
		$reset_key = get_password_reset_key( $this->user_object );
		remove_filter( 'allow_password_reset', '__return_true', 999999999 );
		remove_filter( 'notification/trigger/wordpress/user_password_reset_request/bail_for_registration', '__return_true', 999999999 );

		if ( is_wp_error( $reset_key ) ) {
			notification_log( 'Core', 'error', 'User registration trigger error: ' . $reset_key->get_error_message() );
			return '';
		}

		return $reset_key;

	}

}
