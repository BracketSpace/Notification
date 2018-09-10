<?php
/**
 * Plugin trigger abstract
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Post trigger class
 */
abstract class PluginTrigger extends Abstracts\Trigger {

	/**
	 * Constructor
	 *
	 * @param array $params trigger configuration params.
	 */
	public function __construct( $params = array() ) {

		parent::__construct( $params['slug'], $params['name'] );

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'plugin_name',
			'name'        => __( 'Plugin name', 'notification' ),
			'description' => __( 'Name deactivated plugin.', 'notification' ),
			'resolver'    => function( $trigger ) {
				return $trigger->plugin['Name'];
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'plugin_author_name',
			'name'        => __( 'Plugin author name', 'notification' ),
			'description' => __( 'Author deactivated plugin.', 'notification' ),
			'resolver'    => function( $trigger ) {
				return $trigger->plugin['AuthorName'];
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'plugin_version',
			'name'        => __( 'Plugin version', 'notification' ),
			'description' => __( '1.0.0', 'notification' ),
			'example' => true,
			'resolver'    => function( $trigger ) {
				return $trigger->plugin['Version'];
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'plugin_url',
			'name'        => __( 'Plugin adress url', 'notification' ),
			'description' => __( 'http://example.com', 'notification' ),
			'example' => true,
			'resolver'    => function( $trigger ) {
				return $trigger->plugin['PluginURI'];
			},
		) ) );

	}
}
