<?php
/**
 * User deleted trigger
 */

namespace underDEV\Notification\Defaults\Trigger\User;
use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

class UserDeleted extends Abstracts\Trigger {

	public function __construct() {

		parent::__construct( 'wordpress/user_deleted', 'User deleted' );

		$this->add_action( 'delete_user', 10, 2 );
		$this->set_group( 'User' );
		$this->set_description( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' );

	}

	public function action() {

		$this->date_format = get_option( 'date_format' );
		$this->time_format = get_option( 'time_format' );
		$this->user_id = $this->callback_args[1]->ID;
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

		$this->add_merge_tag( new MergeTag\User\UserDeletedDatetime( $this ) );


    }

}
