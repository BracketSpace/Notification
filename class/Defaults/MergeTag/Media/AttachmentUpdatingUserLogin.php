<?php
/**
 * Attachment updating user login merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Media;

use underDEV\Notification\Defaults\MergeTag\StringTag;


/**
 * Attachment updating user login merge tag class
 */
class AttachmentUpdatingUserLogin extends StringTag {

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
			'slug'        => 'attachment_updating_user_login',
			'name'        => __( 'Attachment author email' ),
			'description' => __( 'Will be resolved to an attachment updating user login' ),
			'resolver'    => function() {
				return get_the_author_meta( 'user_login', $this->trigger->updating_user );
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {
		return isset( $this->trigger->updating_user );
	}

}
