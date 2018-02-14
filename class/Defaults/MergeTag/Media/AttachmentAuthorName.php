<?php
/**
 * Attachment author name merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment author name merge tag class
 */
class AttachmentAuthorName extends StringTag {

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
			'slug'        => 'attachment_author_name',
			'name'        => __( 'Attachment author name' ),
			'description' => __( 'Will be resolved to an attachment author name' ),
			'resolver'    => function() {
				return get_the_author_meta( 'display_name', $this->trigger->attachment->post_author );
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
