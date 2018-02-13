<?php
/**
 * Attachment ID merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\IntegerTag;


/**
 * Attachment ID merge tag class
 */
class AttachmentID extends IntegerTag {

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
			'slug'        => 'attachment_ID',
			'name'        => __( 'Attachment ID' ),
			'description' => __( 'Will be resolved to an attachment ID' ),
			'resolver'    => function() {
				return $this->trigger->attachment->ID;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {

		return isset( $this->trigger->attachment->ID );

	}

}
