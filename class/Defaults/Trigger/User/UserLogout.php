<?php
/**
 * User logout trigger
 */

namespace underDEV\Notification\Defaults\Trigger\User;
use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

class UserLogout extends Abstracts\Trigger {

	public function __construct() {

		parent::__construct( 'wordpress/user_logout', 'User logout' );

		$this->add_action( 'wp_logout', 10, 2 );
		$this->set_group( 'User' );
		$this->set_description( 'Fires when user log out from Wordpress' );

	}

	public function action() {

		$this->user_id = get_current_user_id();
		$this->user_object = get_userdata( $this->user_id );
		$this->user_meta = get_user_meta( $this->user_id );

	}

	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\User\UserID( $this ) );

    	$this->add_merge_tag( new MergeTag\User\UserLogin( $this ) );

        $this->add_merge_tag( new MergeTag\User\UserEmail( $this ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( $this ) );

        $this->add_merge_tag( new MergeTag\User\UserFirstName( $this ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( $this ) );

		$this->add_merge_tag( new MergeTag\User\UserRegistered( $this ) );

		$this->add_merge_tag( new MergeTag\User\UserRole( $this ) );

		$this->add_merge_tag( new MergeTag\User\UserBio( $this ) );

		$this->add_merge_tag( new MergeTag\User\UserLogoutDatetime( $this ) );

    }

}
