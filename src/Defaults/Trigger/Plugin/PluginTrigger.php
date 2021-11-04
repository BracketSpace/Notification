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
 * Plugin trigger class
 */
abstract class PluginTrigger extends Abstracts\Trigger {

	/**
	 * Plugin details array
	 *
	 * @var array
	 */
	public $plugin;

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'plugin_name',
			'name'        => __( 'Plugin name', 'notification' ),
			'description' => __( 'Akismet', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->plugin['Name'];
			},
			'group'       => __( 'Plugin', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'plugin_author_name',
			'name'        => __( 'Plugin author name', 'notification' ),
			'description' => __( 'Automattic', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->plugin['AuthorName'];
			},
			'group'       => __( 'Plugin', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'plugin_version',
			'name'        => __( 'Plugin version', 'notification' ),
			'description' => __( '1.0.0', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->plugin['Version'];
			},
			'group'       => __( 'Plugin', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'plugin_url',
			'name'        => __( 'Plugin website address', 'notification' ),
			'description' => __( 'https://wordpress.org/plugins/example', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->plugin['PluginURI'];
			},
			'group'       => __( 'Plugin', 'notification' ),
		] ) );

	}
}
