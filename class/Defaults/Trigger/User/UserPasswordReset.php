<?php
/**
 * User password reset trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * User password reset trigger class
 */
class UserPasswordReset extends Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'wordpress/password_reset', __( 'User password reset', 'notification' ) );

		$this->add_action( 'password_reset', 10, 1 );
		$this->set_group( __( 'User', 'notification' ) );
		$this->set_description( __( 'Fires when user reset his password', 'notification' ) );

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
		$this->user_deleted_datetime    = time();

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\User\UserID() );
    	$this->add_merge_tag( new MergeTag\User\UserLogin() );
        $this->add_merge_tag( new MergeTag\User\UserEmail() );
		$this->add_merge_tag( new MergeTag\User\UserNicename() );
		$this->add_merge_tag( new MergeTag\User\UserDisplayName() );
        $this->add_merge_tag( new MergeTag\User\UserFirstName() );
		$this->add_merge_tag( new MergeTag\User\UserLastName() );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => 'user_registered_datetime',
			'name' => __( 'User registration date', 'notification' ),
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserRole() );
		$this->add_merge_tag( new MergeTag\User\UserBio() );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => 'user_password_reset_datetime',
			'name' => __( 'User password reset time', 'notification' ),
		) ) );

    }

}
