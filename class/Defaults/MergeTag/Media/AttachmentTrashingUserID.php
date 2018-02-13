<?php
/**
 * Attachment trashing user ID merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\IntegerTag;


/**
 * Attachment trashing user ID merge tag class
 */
class AttachmentTrashingUserID extends IntegerTag {

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
			'slug'        => 'attachment_trashing_user_ID',
			'name'        => __( 'Attachment trashing user ID' ),
			'description' => __( 'Will be resolved to an attachment trashing user ID' ),
			'resolver'    => function() {
				return $this->trigger->trashing_user;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {

		return isset( $this->trigger->trashing_user );

	}

}
