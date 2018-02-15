<?php
/**
 * Media added trigger
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Trigger\Media;

use underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts;

/**
 * Media added trigger class
 */
class MediaAdded extends Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'wordpress/media_added',  __( 'Media added' ) );

		$this->add_action( 'add_attachment', 10, 2 );
		$this->set_group( __( 'Media' ) );
		$this->set_description( __( 'Fires when new attachment is added' ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @return void
	 */
	public function action() {

		$this->attachment  = get_post( $this->callback_args[0] );
		$this->user_id     = $this->attachment->post_author;
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
		$this->add_merge_tag( new MergeTag\Media\AttachmentDate() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentMimeType() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentDirectUrl() );

		$this->add_merge_tag( new MergeTag\User\UserID( 'attachment_author_user_ID', __( 'Attachment author user ID' ) ) );
    	$this->add_merge_tag( new MergeTag\User\UserLogin( 'attachment_author_user_login', __( 'Attachment author user login' ) ) );
        $this->add_merge_tag( new MergeTag\User\UserEmail( 'attachment_author_user_email', __( 'Attachment author user email' ) ) );
		$this->add_merge_tag( new MergeTag\User\UserNicename( 'attachment_author_user_nicename', __( 'Attachment author user nicename' ) ) );
        $this->add_merge_tag( new MergeTag\User\UserFirstName( 'attachment_author_user_firstname', __( 'Attachment author user first name' ) ) );
		$this->add_merge_tag( new MergeTag\User\UserLastName( 'attachment_author_user_lastname', __( 'Attachment author user last name' ) ) );

    }

}
