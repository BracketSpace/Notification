<?php
/**
 * User login trigger
 */

namespace underDEV\Notification\Defaults\Trigger\User;
use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

class UserLogin extends Abstracts\Trigger {

	public function __construct() {

		parent::__construct( 'wordpress/user_login', 'User login' );

		$this->add_action( 'wp_login', 10, 2 );
		$this->set_group( 'User' );
		$this->set_description( 'Fires when user log into Wordpress' );

	}

	public function action() {

        $this->user_object = $this->callback_args[1];
        
	}

	public function merge_tags() {

    	$this->add_merge_tag( new MergeTag\User\UserLogin( $this ) );
        
        $this->add_merge_tag( new MergeTag\User\UserEmail( $this ) );
        
        $this->add_merge_tag( new MergeTag\User\UserNicename( $this ) );
        
        $this->add_merge_tag( new MergeTag\User\UserRegistered( $this ) );

    }

}
