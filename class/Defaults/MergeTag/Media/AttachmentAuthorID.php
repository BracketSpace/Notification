<?php
/**
 * Attachment author ID merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\IntegerTag;


/**
 * Attachment author ID merge tag class
 */
class AttachmentAuthorID extends IntegerTag {

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
			'slug'        => 'attachment_author_ID',
			'name'        => __( 'Attachment author ID' ),
			'description' => __( 'Will be resolved to an attachment author ID' ),
			'resolver'    => function() {
				return $this->trigger->attachment->post_author;
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
