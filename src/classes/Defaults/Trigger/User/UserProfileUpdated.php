<?php
/**
 * User profile updated trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User profile updated trigger class
 */
class UserProfileUpdated extends UserTrigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'user/profile_updated', __( 'User profile updated', 'notification' ) );

		$this->add_action( 'profile_update', 10, 2 );

		$this->set_description( __( 'Fires when user updates his profile', 'notification' ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @param integer $user_id User ID.
	 * @return void
	 */
	public function action( $user_id ) {

		$this->user_id     = $user_id;
		$this->user_object = get_userdata( $this->user_id );
		$this->user_meta   = get_user_meta( $this->user_id );

		$this->user_registered_datetime      = strtotime( $this->user_object->user_registered );
		$this->user_profile_updated_datetime = $this->cache( 'timestamp', time() );

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
			'slug' => 'user_profile_updated_datetime',
			'name' => __( 'User profile update time', 'notification' ),
		] ) );

	}

}
