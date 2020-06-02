<?php
/**
 * Privacy trigger abstract
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Privacy;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Privacy trigger abstract class
 */
abstract class PrivacyTrigger extends Abstracts\Trigger {

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'request_author',
			'name'        => __( 'Request user', 'notification' ),
			'description' => __( 'John Doe', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->user_object->data->display_name;
			},
			'group'       => __( 'User', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'request_author_email',
			'name'        => __( 'Request user email', 'notification' ),
			'description' => __( 'john@doe.com', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->request->post_title;
			},
			'group'       => __( 'User', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'data_operation_time',
			'name' => __( 'Data erase date and time', 'notification' ),
		] ) );

	}
}
