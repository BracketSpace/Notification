<?php
/**
 * Attachment author email merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\EmailTag;


/**
 * Attachment author email merge tag class
 */
class AttachmentAuthorEmail extends EmailTag {

	/**
	 * Receives Trigger object from Trigger class
	 *
	 * @var private object $trigger
	 */
	private $trigger;

	/**
	 * Constructor
	 *
	 * @param object $trigger Trigger object to access data from.
	 */
	public function __construct( $trigger ) {

		$this->trigger = $trigger;


		parent::__construct( array(
			'slug'        => 'attachment_author_email',
			'name'        => __( 'Attachment author email' ),
			'description' => __( 'Will be resolved to an attachment author email' ),
			'resolver'    => function() {
				return get_the_author_meta( 'user_email', $this->trigger->attachment->post_author );
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {

		return isset( $this->trigger->attachment->post_author );

	}

}
