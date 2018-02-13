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

		parent::__construct( 'wordpress/media_added', 'Media added' );

		$this->add_action( 'add_attachment', 10, 2 );
		$this->set_group( 'Media' );
		$this->set_description( 'Fires when new attachment is added' );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @return void
	 */
	public function action() {

		$this->attachment = get_post( $this->callback_args[0] );

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


    }

}
