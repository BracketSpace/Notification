<?php
/**
 * User deleted trigger
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Trigger\User;

use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

/**
 * User deleted trigger class
 */
class UserDeleted extends Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'wordpress/user_deleted', __( 'User deleted', 'notification' ) );

		$this->add_action( 'delete_user', 10, 2 );
		$this->set_group( __( 'User', 'notification' ) );
		$this->set_description( __( 'Fires when user account is deleted', 'notification' ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @return void
	 */
	public function action() {

		$this->user_id     = $this->callback_args[1]->ID;
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
        $this->add_merge_tag( new MergeTag\User\UserFirstName() );
		$this->add_merge_tag( new MergeTag\User\UserLastName() );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => 'user_registered_datetime',
			'name' => __( 'User registration date', 'notification' ),
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserRole() );
		$this->add_merge_tag( new MergeTag\User\UserBio() );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => 'user_deleted_datetime',
			'name' => __( 'User deletion time', 'notification' ),
		) ) );

    }

}
