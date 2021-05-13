<?php
/**
 * Privacy trigger abstract
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Privacy;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Privacy trigger abstract class
 */
abstract class PrivacyTrigger extends Abstracts\Trigger {

	/**
	 * User request object
	 *
	 * @var \WP_User_Request
	 */
	public $request;

	/**
	 * Request user object
	 *
	 * @var \WP_User|false
	 */
	public $user_object;

	/**
	 * Data operation date and time
	 *
	 * @var string
	 */
	public $data_operation_time;

	/**
	 * Constructor
	 *
	 * @param string $slug Slug.
	 * @param string $name Name.
	 */
	public function __construct( $slug, $name ) {

		parent::__construct( $slug, $name );

		$this->set_group( __( 'Privacy', 'notification' ) );

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		// Data owner.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'  => 'data_owner_ID',
			'name'  => __( 'Data owner ID', 'notification' ),
			'group' => __( 'Data owner', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'  => 'data_owner_email',
			'name'  => __( 'Data owner email', 'notification' ),
			'group' => __( 'Data owner', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'  => 'data_owner_login',
			'name'  => __( 'Data owner login', 'notification' ),
			'group' => __( 'Data owner', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'  => 'data_owner_first_name',
			'name'  => __( 'Data owner first name', 'notification' ),
			'group' => __( 'Data owner', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'  => 'data_owner_last_name',
			'name'  => __( 'Data owner last name', 'notification' ),
			'group' => __( 'Data owner', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'  => 'data_owner_nicename',
			'name'  => __( 'Data owner nicename', 'notification' ),
			'group' => __( 'Data owner', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'  => 'data_owner_display_name',
			'name'  => __( 'Data owner display name', 'notification' ),
			'group' => __( 'Data owner', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserRole( [
			'slug'  => 'data_owner_role',
			'name'  => __( 'Data owner role', 'notification' ),
			'group' => __( 'Data owner', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\Avatar( [
			'slug'  => 'data_owner_avatar',
			'name'  => __( 'Data owner avatar', 'notification' ),
			'group' => __( 'Data owner', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\AvatarUrl( [
			'slug'  => 'data_owner_avatar_url',
			'name'  => __( 'Data owner avatar url', 'notification' ),
			'group' => __( 'Data owner', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserBio( [
			'slug'  => 'data_owner_bio',
			'name'  => __( 'Data owner bio', 'notification' ),
			'group' => __( 'Data owner', 'notification' ),
		] ) );

		// Date and time.
		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'data_operation_time',
			'name' => __( 'Operation date and time', 'notification' ),
		] ) );

	}
}
