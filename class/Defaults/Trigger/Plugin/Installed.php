<?php
/**
 * WordPress plugin installed trigger.
 *
 * @package notification.
 */

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Installed plugin trigger class.
 */
class Installed extends PluginTrigger {

	/**
	 * Constructor.
	 */
	public function __construct( ) {

		parent::__construct( 'wordpress/plugin/install', __( 'Plugin installed', 'notification' ) );

		$this->add_action( 'upgrader_process_complete', 10, 2 );
		$this->set_group( __( 'Plugin', 'notification' ) );
		$this->set_description( __( 'Fires when plugin is installed', 'notification' ) );

	}

	/**
	 * Trigger action.
	 *
	 * @param  string $obj Plugin info.
	 * @param  array  $type Plugin action and type information.
	 * @return mixed void or false if no notifications should be sent.
	 */
	public function action( $obj, $type ) {

		if ( !isset($type['type']) || $type['type'] !== 'plugin' ) {
			return false;
		}

		if( $type['action'] === 'install' ) {

			if ( !$obj->plugin_info( ) ) {
				return false;
			}

			$plugin_dir                     = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $obj->plugin_info( );
			$this->plugin                   = get_plugin_data( $plugin_dir );
			$this->plugin_install_date_time = strtotime( 'now' );

		}
	}

	/**
	 * Registers attached merge tags.
	 *
	 * @return void.
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
