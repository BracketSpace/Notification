<?php
/**
 * Comment ID merge tag
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag\Comment;

use underDEV\Notification\Defaults\MergeTag\IntegerTag;


/**
 * comment ID merge tag class
 */
class CommentID extends IntegerTag {

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
			'slug'        => 'comment_ID',
			'name'        => __( 'Comment ID' ),
			'description' => __( 'Will be resolved to an comment ID' ),
			'resolver'    => function() {
				return $this->trigger->comment_ID;
			},
		) );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements( ) {

		return isset( $this->trigger->comment_ID );

	}

}
