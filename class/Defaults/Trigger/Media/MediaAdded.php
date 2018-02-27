<?php
/**
 * Media added trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Media;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Media added trigger class
 */
class MediaAdded extends Abstracts\Trigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'wordpress/media_added',  __( 'Media added', 'notification' ) );

		$this->add_action( 'add_attachment', 10, 2 );
		$this->set_group( __( 'Media', 'notification' ) );
		$this->set_description( __( 'Fires when new attachment is added', 'notification' ) );

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
			'name' => __( 'Attachment creation date', 'notification' ),
		) ) );

		// Author.
		$this->add_merge_tag( new MergeTag\User\UserID( array(
			'slug' => 'attachment_author_user_ID',
			'name' => __( 'Attachment author user ID', 'notification' ),
		) ) );

    	$this->add_merge_tag( new MergeTag\User\UserLogin( array(
			'slug' => 'attachment_author_user_login',
			'name' => __( 'Attachment author user login', 'notification' ),
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserEmail( array(
			'slug' => 'attachment_author_user_email',
			'name' => __( 'Attachment author user email', 'notification' ),
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( array(
			'slug' => 'attachment_author_user_nicename',
			'name' => __( 'Attachment author user nicename', 'notification' ),
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserFirstName( array(
			'slug' => 'attachment_author_user_firstname',
			'name' => __( 'Attachment author user first name', 'notification' ),
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( array(
			'slug' => 'attachment_author_user_lastname',
			'name' => __( 'Attachment author user last name', 'notification' ),
		) ) );

    }

}
