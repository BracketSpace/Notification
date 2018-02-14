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

		parent::__construct( 'wordpress/media_trashed', 'Media trashed' );

		$this->add_action( 'delete_attachment', 10, 2 );
		$this->set_group( 'Media' );
		$this->set_description( 'Fires when attachment is removed' );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @return void
	 */
	public function action() {

		$this->attachment = get_post( $this->callback_args[0] );
		$this->trashing_user = get_current_user_id();

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\Media\AttachmentID( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentPage( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentTitle( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentDate( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentMimeType( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentDirectUrl( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentAuthorID( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentAuthorName( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentAuthorEmail( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentAuthorLogin( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentTrashingUserID( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentTrashingUserName( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentTrashingUserEmail( $this ) );
		$this->add_merge_tag( new MergeTag\Media\AttachmentTrashingUserLogin( $this ) );

    }

}
