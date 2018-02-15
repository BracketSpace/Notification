<?php
/**
 * User login trigger
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Trigger\User;

use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

/**
 * User login trigger class
 */
class UserLogin extends Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->date_format      = get_option( 'date_format' );
		$this->time_format      = get_option( 'time_format' );
		$this->date_time_format = $this->date_format . ' ' . $this->time_format;

		parent::__construct( 'wordpress/user_login', __( 'User login' ) );

		$this->add_action( 'wp_login', 10, 2 );
		$this->set_group( __( 'User' ) );
		$this->set_description( __( 'Fires when user log into WordPress' ) );

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
		$this->add_merge_tag( new MergeTag\User\UserRegistered( $this->date_time_format ) );
		$this->add_merge_tag( new MergeTag\User\UserRole() );
		$this->add_merge_tag( new MergeTag\User\UserBio() );
		$this->add_merge_tag( new MergeTag\User\UserLoggedInDatetime( $this->date_time_format ) );

    }

}
