<?php
/**
 * WordPress Active Plugin trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\WordPress;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Active Plugin trigger class
 */
class ActivePlugin extends Abstracts\Trigger{

	/**
	 * Constructor
	 */
	public function __construct(){

		parent::__construct( 'wordpress/active_plugin',  __( 'Plugin Active', 'notification' ) );

		$this->add_action( 'activated_plugin', 10, 2 );
		$this->set_group( __( 'WordPress', 'notification' ) );
		$this->set_description( __( 'Fires when plugin can be active', 'notification' ) );

	}

	/**
	 * Gets specific update type
	 *
	 * @param  string $plugin PluginPath.
	 * @return void
	 */
	public function action($plugin)
	{
		$this->plugin = $plugin;
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
			'name'        => __( 'Author Name', 'notification' ),
			'description' => __( 'Author of the plugin.', 'notification' ),
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
