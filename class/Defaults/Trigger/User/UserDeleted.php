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

        $this->user_id = $this->callback_args[0];
        $this->user_object = get_userdata( $this->user_id );
        
	}

	public function merge_tags() {

    	$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'user_login',
			'name'        => __( 'User login' ),
			'description' => __( 'Will be resolved to a user login' ),
			'resolver'    => function() {
				return $this->user_object->user_login;
			}
        ) ) );
        
        $this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'user_email',
			'name'        => __( 'User email' ),
			'description' => __( 'Will be resolved to a user email' ),
			'resolver'    => function() {
				return $this->user_object->user_email;
			}
        ) ) );
        
        $this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'user_nicename',
			'name'        => __( 'User nicename' ),
			'description' => __( 'Will be resolved to a user nicename' ),
			'resolver'    => function() {
				return $this->user_object->user_nicename;
			}
        ) ) );
        
        $this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'user_registered',
			'name'        => __( 'User registered' ),
			'description' => __( 'Will be resolved to a user registration date' ),
			'resolver'    => function() {
				return $this->user_object->user_registered;
			}
    	) ) );

    }

}
