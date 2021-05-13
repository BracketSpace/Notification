<?php
/**
 * User login failed trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User login failed trigger class
 */
class UserLoginFailed extends UserTrigger {

	/**
	 * User login failure date and time
	 *
	 * @var int|false
	 */
	public $user_login_failed_datetime;

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'user/login_failed', __( 'User login failed', 'notification' ) );

		$this->add_action( 'wp_login_failed', 10, 1 );

		$this->set_description( __( 'Fires when user login failed', 'notification' ) );

	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $username username.
	 * @return mixed
	 */
	public function context( $username ) {

		$user = get_user_by( 'login', $username );

		// Bail if no user has been found to limit the spam login notifications.
		if ( ! $user ) {
			return false;
		}

		$this->user_id     = $user->ID;
		$this->user_object = get_userdata( $this->user_id );

		$this->user_registered_datetime   = strtotime( $this->user_object->user_registered );
		$this->user_login_failed_datetime = time();

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
		$this->add_merge_tag( new MergeTag\User\UserBio() );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'user_login_failed_datetime',
			'name' => __( 'User login failed datetime', 'notification' ),
		] ) );

	}

}
