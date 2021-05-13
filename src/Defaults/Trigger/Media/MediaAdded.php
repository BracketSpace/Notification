<?php
/**
 * Media added trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Media;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Media added trigger class
 */
class MediaAdded extends MediaTrigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'media/added', __( 'Media added', 'notification' ) );

		$this->add_action( 'add_attachment', 10, 1 );
		$this->set_description( __( 'Fires when new attachment is added', 'notification' ) );

	}

	/**
	 * Sets trigger's context
	 *
	 * @param integer $attachment_id Attachment Post ID.
	 * @return void
	 */
	public function context( $attachment_id ) {

		$this->attachment  = get_post( $attachment_id );
		$this->user_id     = (int) $this->attachment->post_author;
		$this->user_object = get_userdata( $this->user_id );

		$this->attachment_creation_date = strtotime( $this->attachment->post_date_gmt );

	}
}
