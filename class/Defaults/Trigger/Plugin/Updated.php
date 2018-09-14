<?php
/**
 * WordPress plugin updated trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Updated plugin trigger class
 */
class Updated extends PluginTrigger {

	/**
	 * Constructor
	 */
	public function __construct( ) {

		parent::__construct( 'wordpress/plugin/update', __( 'Plugin updated', 'notification' ) );

		$this->add_action( 'upgrader_process_complete', 10, 2 );
		$this->set_group( __( 'Plugin', 'notification' ) );
		$this->set_description( __( 'Fires when plugin is updated', 'notification' ) );

	}

	/**
	 * Trigger action
	 *
	 * @param  string $obj Plugin info.
	 * @param  array  $type Plugin action and type information.
	 * @return void
	 */
	public function action( $obj, $type ) {

		if ( !isset( $type['type'] ) || $type['type'] !== 'plugin' )
			return;

		if ( isset($type['bulk']) && $type['bulk'] == true ) {

			if ( $type['action'] === 'update' ) {
				$path = $obj->plugin_info( );

				if ( !$path )
					return;

				$this->version_before          = $obj->skin->plugin_info['Version'];
				$plugin_dir                    = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $path;
				$this->plugin                  = get_plugin_data( $plugin_dir );
				$this->plugin_update_date_time = strtotime( 'now' );
			}
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
			'slug' => 'plugin_update_date_time',
			'name' => __( 'Plugin updated date and time', 'notification' ),

		) ) );

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'before_version',
			'name'        => __( 'Plugin version before update', 'notification' ),
			'description' => __( '1.0.0', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->version_before;
			},
		) ) );
	}
}
