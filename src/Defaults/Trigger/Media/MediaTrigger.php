<?php
/**
 * Media trigger abstract
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Media;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Media trigger class
 */
abstract class MediaTrigger extends Abstracts\Trigger {

	/**
	 * Attachment post object
	 *
	 * @var \WP_Post
	 */
	public $attachment;

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
	 * Attachment creation date and time
	 *
	 * @var int|false
	 */
	public $attachment_creation_date;

	/**
	 * Constructor
	 *
	 * @param string $slug $params trigger slug.
	 * @param string $name $params trigger name.
	 */
	public function __construct( $slug, $name ) {
		parent::__construct( $slug, $name );
		$this->set_group( __( 'Media', 'notification' ) );
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\Media\AttachmentID() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentPage() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentTitle() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentMimeType() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentDirectUrl() );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug'  => 'attachment_creation_date',
			'name'  => __( 'Attachment creation date', 'notification' ),
			'group' => __( 'Author', 'notification' ),
		] ) );

		// Author.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'  => 'attachment_author_user_ID',
			'name'  => __( 'Attachment author user ID', 'notification' ),
			'group' => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'  => 'attachment_author_user_login',
			'name'  => __( 'Attachment author user login', 'notification' ),
			'group' => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'  => 'attachment_author_user_email',
			'name'  => __( 'Attachment author user email', 'notification' ),
			'group' => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'  => 'attachment_author_user_nicename',
			'name'  => __( 'Attachment author user nicename', 'notification' ),
			'group' => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'  => 'attachment_author_user_display_name',
			'name'  => __( 'Attachment author user display name', 'notification' ),
			'group' => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'  => 'attachment_author_user_firstname',
			'name'  => __( 'Attachment author user first name', 'notification' ),
			'group' => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'  => 'attachment_author_user_lastname',
			'name'  => __( 'Attachment author user last name', 'notification' ),
			'group' => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\Avatar( [
			'slug'  => 'attachment_author_user_avatar',
			'name'  => __( 'Attachment author user avatar', 'notification' ),
			'group' => __( 'Author', 'notification' ),
		] ) );

	}

}
