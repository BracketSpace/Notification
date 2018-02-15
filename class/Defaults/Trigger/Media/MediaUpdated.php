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

		$this->date_format      = get_option( 'date_format' );
		$this->time_format      = get_option( 'time_format' );
		$this->date_time_format = $this->date_format . ' ' . $this->time_format;

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

		$this->attachment  = get_post( $this->callback_args[0] );
		$this->user_id     = get_current_user_id();
		$this->user_object = get_userdata( $this->user_id );

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
		$this->add_merge_tag( new MergeTag\Media\AttachmentDate( $this->date_time_format ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentMimeType() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentDirectUrl() );

		$this->add_merge_tag( new MergeTag\User\UserID( 'attachment_author_user_ID', __( 'Attachment author user ID' ) ) );
    	$this->add_merge_tag( new MergeTag\User\UserLogin( 'attachment_author_user_login', __( 'Attachment author user login' ) ) );
        $this->add_merge_tag( new MergeTag\User\UserEmail( 'attachment_author_user_email', __( 'Attachment author user email' ) ) );
		$this->add_merge_tag( new MergeTag\User\UserNicename( 'attachment_author_user_nicename', __( 'Attachment author user nicename' ) ) );
        $this->add_merge_tag( new MergeTag\User\UserFirstName( 'attachment_author_user_firstname', __( 'Attachment author user first name' ) ) );
		$this->add_merge_tag( new MergeTag\User\UserLastName( 'attachment_author_user_lastname', __( 'Attachment author user last name' ) ) );

		$this->add_merge_tag( new MergeTag\User\UserID( 'attachment_updating_user_ID', __( 'Attachment updating user ID' ) ) );
    	$this->add_merge_tag( new MergeTag\User\UserLogin( 'attachment_updating_user_login', __( 'Attachment updating user login' ) ) );
        $this->add_merge_tag( new MergeTag\User\UserEmail( 'attachment_updating_user_email', __( 'Attachment updating user email' ) ) );
		$this->add_merge_tag( new MergeTag\User\UserNicename( 'attachment_updating_user_nicename', __( 'Attachment updating user nicename' ) ) );
        $this->add_merge_tag( new MergeTag\User\UserFirstName( 'attachment_updating_user_firstname', __( 'Attachment updating user first name' ) ) );
		$this->add_merge_tag( new MergeTag\User\UserLastName( 'attachment_updating_user_lastname', __( 'Attachment updating user last name' ) ) );

    }

}
