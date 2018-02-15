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

		$this->attachment = get_post( $this->callback_args[0] );
		$this->updating_user = get_current_user_id();

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
		$this->add_merge_tag( new MergeTag\Media\AttachmentAuthorID() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentAuthorName() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentAuthorEmail() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentAuthorLogin() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentUpdatingUserID() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentUpdatingUserName() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentUpdatingUserEmail() );
		$this->add_merge_tag( new MergeTag\Media\AttachmentUpdatingUserLogin() );

    }

}
