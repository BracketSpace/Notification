<?php
/**
 * Media trashed trigger
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Trigger\Media;

use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

/**
 * Media trashed trigger class
 */
class MediaTrashed extends Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->date_format      = get_option( 'date_format' );
		$this->time_format      = get_option( 'time_format' );
		$this->date_time_format = $this->date_format . ' ' . $this->time_format;

		parent::__construct( 'wordpress/media_trashed',  __( 'Media trashed' ) );

		$this->add_action( 'delete_attachment', 10, 2 );
		$this->set_group( __( 'Media' ) );
		$this->set_description( __( 'Fires when attachment is removed' ) );

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

		$this->add_merge_tag( new MergeTag\User\UserID( 'attachment_trashing_user_ID', __( 'Attachment trashing user ID' ) ) );
    	$this->add_merge_tag( new MergeTag\User\UserLogin( 'attachment_trashing_user_login', __( 'Attachment trashing user login' ) ) );
        $this->add_merge_tag( new MergeTag\User\UserEmail( 'attachment_trashing_user_email', __( 'Attachment trashing user email' ) ) );
		$this->add_merge_tag( new MergeTag\User\UserNicename( 'attachment_trashing_user_nicename', __( 'Attachment trashing user nicename' ) ) );
        $this->add_merge_tag( new MergeTag\User\UserFirstName( 'attachment_trashing_user_firstname', __( 'Attachment trashing user first name' ) ) );
		$this->add_merge_tag( new MergeTag\User\UserLastName( 'attachment_trashing_user_lastname', __( 'Attachment trashing user last name' ) ) );

    }

}
