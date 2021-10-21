<?php
/**
 * User trigger abstract
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\User;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * User trigger class
 */
abstract class UserTrigger extends Abstracts\Trigger {

	/**
	 * User ID
	 *
	 * @var int
	 */
	public $user_id;

	/**
	 * User object
	 *
	 * @var \WP_User
	 */
	public $user_object;

	/**
	 * User registration date and time
	 *
	 * @var int|false
	 */
	public $user_registered_datetime;

	/**
	 * Constructor
	 *
	 * @param string $slug $params trigger slug.
	 * @param string $name $params trigger name.
	 */
	public function __construct( $slug, $name ) {
		parent::__construct( $slug, $name );
		$this->set_group( __( 'User', 'notification' ) );
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
		$this->add_merge_tag( new MergeTag\User\UserRole() );
		$this->add_merge_tag( new MergeTag\User\Avatar() );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'user_registered_datetime',
			'name' => __( 'User registration date', 'notification' ),
		] ) );

	}

}
