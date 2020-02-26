<?php
/**
 * User password changed trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User password changed trigger class
 */
class UserPasswordChanged extends UserTrigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'user/password_changed', __( 'User password changed', 'notification' ) );

		$this->add_action( 'password_reset', 10, 1 );

		$this->set_description( __( 'Fires when user changed his password', 'notification' ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @param object $user User object.
	 * @return void
	 */
	public function action( $user ) {

		$this->user_id     = $user->ID;
		$this->user_object = get_userdata( $this->user_id );
		$this->user_meta   = get_user_meta( $this->user_id );

		$this->user_registered_datetime = strtotime( $this->user_object->user_registered );
		$this->password_change_datetime = $this->cache( 'timestamp', time() );

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
			'slug' => 'password_change_datetime',
			'name' => __( 'Password change date', 'notification' ),
		] ) );

	}

}
