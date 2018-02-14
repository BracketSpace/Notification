<?php
/**
 * Attachment trashing user email merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\EmailTag;


/**
 * Attachment trashing user email merge tag class
 */
class AttachmentTrashingUserEmail extends EmailTag {

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
			'slug'        => 'attachment_trashing_user_name',
			'name'        => __( 'Attachment trashing user email' ),
			'description' => __( 'Will be resolved to an attachment trashing user email' ),
			'resolver'    => function() {
				return get_the_author_meta( 'user_email', $this->trigger->trashing_user );
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
