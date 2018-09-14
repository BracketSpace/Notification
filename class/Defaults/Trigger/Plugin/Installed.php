<?php
/**
 * WordPress plugin installed trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Installed plugin trigger class
 */
class Installed extends PluginTrigger {

	/**
	 * Constructor
	 */
	public function __construct( ) {

		parent::__construct( 'wordpress/plugin/install', __( 'Plugin installed', 'notification' ) );

		$this->add_action( 'upgrader_process_complete', 10, 2 );
		$this->set_group( __( 'Plugin', 'notification' ) );
		$this->set_description( __( 'Fires when plugin is installed', 'notification' ) );

	}

	/**
	 * Trigger action
	 *
	 * @param  string $obj Plugin info.
	 * @param  object $type Plugin action and type information.
	 * @return void
	 */
	public function action( $obj, $type ) {

		if ( !isset($type['type']) || $type['type'] !== 'plugin' )
			return;

		if( $type['action'] === 'install' ) {
			$path = $obj->plugin_info( );

			if ( !$path )
				return;

			$plugin_dir                     = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $path;
			$this->plugin                   = get_plugin_data( $plugin_dir );
			$this->plugin_install_date_time = strtotime( 'now' );
		}
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags( )
	{

		parent::merge_tags( );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => 'plugin_install_date_time',
			'name' => __( 'Plugin installed date and time', 'notification' ),

		) ) );
	}
}
