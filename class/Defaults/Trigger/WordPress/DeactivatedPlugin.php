<?php

/**
 * WordPress Deactivate Plugin trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\WordPress;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * WordPressDeactivate Plugin trigger class
 */
class DeactivatedPlugin extends Abstracts\Trigger{

	/**
	 * Constructor
	 */
	public function __construct(){

		parent::__construct( 'wordpress/deactived_plugin',  __( 'Plugin Deactivated', 'notification' ) );

		$this->add_action( 'deactivated_plugin', 10, 2 );
		$this->set_group( __( 'WordPress', 'notification' ) );
		$this->set_description( __( 'Fires when plugin is deactivated', 'notification' ) );
	}

	/**
	 * Gets specific update type
	 *
	 * @since  5.1.5
	 * @param  string $plugin
	 * @return string
	 */
	public function action($plugin, $network_activation)
	{
		$this->plugin = $plugin;
		$this->plugin_path = $plugin;
		$this->plugin_network = $network_activation;
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags()
	{
		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'plugin_name',
			'name'        => __( 'Plugin Name', 'notification' ),
			'description' => __( 'Name deactivated plugin.', 'notification' ),
			'resolver'    => function( $trigger ) {

				$plugin_dir = ABSPATH . 'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$trigger->plugin;
				$plugin_data = get_plugin_data($plugin_dir);
				return $plugin_data['Name'];
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'plugin_author_name',
			'name'        => __( 'Plugin Author Name', 'notification' ),
			'description' => __( 'Author deactivated plugin.', 'notification' ),
			'resolver'    => function( $trigger ) {

				$plugin_dir = ABSPATH . 'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$trigger->plugin;
				$plugin_data = get_plugin_data($plugin_dir);

				$this->description = $plugin_data['Description'];
				return $plugin_data['AuthorName'];
			},
		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'plugin_version',
			'name'        => __( 'Plugin Version', 'notification' ),
			'description' => __( 'Version deactivated plugin.', 'notification' ),
			'resolver'    => function( $trigger ) {

				$plugin_dir = ABSPATH . 'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$trigger->plugin;
				$plugin_data = get_plugin_data($plugin_dir);

				return $plugin_data['Version'];
			},
		) ) );


		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'plugin_url',
			'name'        => __( 'Plugin adress url', 'notification' ),
			'description' => __( 'Adress url to plugin.', 'notification' ),
			'resolver'    => function( $trigger ) {

				$plugin_dir = ABSPATH . 'wp-content'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$trigger->plugin;
				$plugin_data = get_plugin_data($plugin_dir);

				return $plugin_data['PluginURI'];

			},
		) ) );

	}
}
