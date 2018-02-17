<?php
/**
 * Media updated trigger
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Trigger\Media;

use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

/**
 * Media added trigger class
 */
class MediaUpdated extends Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'wordpress/media_updated',  __( 'Media updated' ) );

		$this->add_action( 'attachment_updated', 10, 2 );
		$this->set_group( __( 'Media' ) );
		$this->set_description( __( 'Fires when attachment is updated' ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @return void
	 */
	public function action() {

		$this->attachment    = get_post( $this->callback_args[0] );
		$this->user_id       = get_current_user_id();
		$this->user_object   = get_userdata( $this->user_id );
		$this->updating_user = get_userdata( get_current_user_id() );

		$this->attachment_creation_date = strtotime( $this->attachment->post_date );

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

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => 'attachment_creation_date',
			'name' => __( 'Attachment creation date' ),
		) ) );

		// Author.
		$this->add_merge_tag( new MergeTag\User\UserID( array(
			'slug' => 'attachment_author_user_ID',
			'name' => __( 'Attachment author user ID' ),
		) ) );

    	$this->add_merge_tag( new MergeTag\User\UserLogin( array(
			'slug' => 'attachment_author_user_login',
			'name' => __( 'Attachment author user login'  ),
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserEmail( array(
			'slug' => 'attachment_author_user_email',
			'name' => __( 'Attachment author user email' ),
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( array(
			'slug' => 'attachment_author_user_nicename',
			'name' => __( 'Attachment author user nicename' ),
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserFirstName( array(
			'slug' => 'attachment_author_user_firstname',
			'name' => __( 'Attachment author user first name' ),
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( array(
			'slug' => 'attachment_author_user_lastname',
			'name' => __( 'Attachment author user last name' ),
		) ) );

		// Updating user.
		$this->add_merge_tag( new MergeTag\User\UserID( array(
			'slug'          => 'attachment_updating_user_ID',
			'name'          => __( 'Attachment updating user ID' ),
			'property_name' => 'updating_user',
		) ) );

    	$this->add_merge_tag( new MergeTag\User\UserLogin( array(
			'slug'          => 'attachment_updating_user_login',
			'name'          => __( 'Attachment updating user login' ),
			'property_name' => 'updating_user',
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserEmail( array(
			'slug'          => 'attachment_updating_user_email',
			'name'          => __( 'Attachment updating user email' ),
			'property_name' => 'updating_user',
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( array(
			'slug'          => 'attachment_updating_user_nicename',
			'name'          => __( 'Attachment updating user nicename' ),
			'property_name' => 'updating_user',
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserFirstName( array(
			'slug'          => 'attachment_updating_user_firstname',
			'name'          => __( 'Attachment updating user first name' ),
			'property_name' => 'updating_user',
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( array(
			'slug'          => 'attachment_updating_user_lastname',
			'name'          => __( 'Attachment updating user last name' ),
			'property_name' => 'updating_user',
		) ) );

    }

}
