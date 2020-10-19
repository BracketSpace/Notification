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
	 * Constructor
	 *
	 * @param string $slug Slug.
	 * @param string $name Name.
	 */
	public function __construct( $slug, $name ) {

		parent::__construct( $slug, $name );

		$this->set_group( __( 'Privacy', 'notification' ) );

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		// Owner data.
		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'owner_data_ID',
			'name'        => __( 'Owner data ID', 'notification' ),
			'description' => __( '25', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->user_object->data->ID;
			},
			'group'       => __( 'Owner data', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'owner_data_login',
			'name'        => __( 'Owner data login', 'notification' ),
			'description' => __( 'johndoe', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->user_object->data->user_login;
			},
			'group'       => __( 'Owner data', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'owner_data_nicename',
			'name'        => __( 'Owner data nicename', 'notification' ),
			'description' => __( 'Johhnie', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->user_object->data->user_nicename;
			},
			'group'       => __( 'Owner data', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'owner_data_email',
			'name'        => __( 'Owner data email', 'notification' ),
			'description' => __( 'john@doe.com', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->user_object->data->user_email;
			},
			'group'       => __( 'Owner data', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'owner_data_display_name',
			'name'        => __( 'Owner data display name', 'notification' ),
			'description' => __( 'John Doe', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->user_object->data->display_name;
			},
			'group'       => __( 'Owner data', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'owner_data_role',
			'name'        => __( 'Owner data role', 'notification' ),
			'description' => __( 'Subscriber', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->user_object->roles[0];
			},
			'group'       => __( 'Owner data', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'data_operation_time',
			'name' => __( 'Operation date and time', 'notification' ),
		] ) );

	}
}
