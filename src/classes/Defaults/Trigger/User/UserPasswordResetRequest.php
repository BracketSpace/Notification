<?php
/**
 * User password change requested trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User password change requested trigger class
 */
class UserPasswordResetRequest extends UserTrigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'user/password_reset_request', __( 'User password reset request', 'notification' ) );

		$this->add_action( 'retrieve_password_key', 10, 2 );

		$this->set_description( __( 'Fires when user requests password change', 'notification' ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @param string $username  username.
	 * @param string $reset_key password reset key.
	 * @return mixed
	 */
	public function action( $username, $reset_key ) {

		$user = get_user_by( 'login', $username );

		/**
		 * Bail if we are handling the registration.
		 * Use the filter to integrate with 3rd party code.
		 */
		if ( ( isset( $_GET['action'] ) && 'register' === $_GET['action'] ) ||  // phpcs:ignore
			apply_filters( 'notification/trigger/wordpress/user_password_reset_request/bail_for_registration', false, $user ) ) {
			return false;
		}

		$this->user_id     = $user->data->ID;
		$this->user_login  = $user->data->user_login;
		$this->user_object = get_userdata( $this->user_id );

		$this->password_reset_key = $reset_key;

		$this->user_registered_datetime        = strtotime( $this->user_object->user_registered );
		$this->password_reset_request_datetime = $this->cache( 'timestamp', time() );

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\User\UserNicename() );
		$this->add_merge_tag( new MergeTag\User\UserDisplayName() );
		$this->add_merge_tag( new MergeTag\User\UserFirstName() );
		$this->add_merge_tag( new MergeTag\User\UserLastName() );
		$this->add_merge_tag( new MergeTag\User\UserPasswordResetLink() );
		$this->add_merge_tag( new MergeTag\User\UserBio() );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'password_reset_request_datetime',
			'name' => __( 'Password reset request date', 'notification' ),
		] ) );

	}

}
