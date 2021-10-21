<?php
/**
 * Media updated trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Media;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Media added trigger class
 */
class MediaUpdated extends MediaTrigger {

	/**
	 * Updating user object
	 *
	 * @var \WP_User
	 */
	public $updating_user;

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'media/updated', __( 'Media updated', 'notification' ) );

		$this->add_action( 'attachment_updated', 10, 1 );
		$this->set_description( __( 'Fires when attachment is updated', 'notification' ) );

	}

	/**
	 * Sets trigger's context
	 *
	 * @param integer $attachment_id Attachment Post ID.
	 * @return void
	 */
	public function context( $attachment_id ) {

		$this->attachment = get_post( $attachment_id );

		$this->user_id = get_current_user_id();

		$this->user_object   = get_userdata( $this->user_id );
		$this->updating_user = get_userdata( $this->user_id );

		$this->attachment_creation_date = strtotime( $this->attachment->post_date_gmt );

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		// Updating user.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'          => 'attachment_updating_user_ID',
			'name'          => __( 'Attachment updating user ID', 'notification' ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'          => 'attachment_updating_user_login',
			'name'          => __( 'Attachment updating user login', 'notification' ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'          => 'attachment_updating_user_email',
			'name'          => __( 'Attachment updating user email', 'notification' ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'          => 'attachment_updating_user_nicename',
			'name'          => __( 'Attachment updating user nicename', 'notification' ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'          => 'attachment_updating_user_firstname',
			'name'          => __( 'Attachment updating user first name', 'notification' ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'          => 'attachment_updating_user_lastname',
			'name'          => __( 'Attachment updating user last name', 'notification' ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

	}

}
