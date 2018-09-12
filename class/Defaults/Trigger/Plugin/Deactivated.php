<?php
/**
 * WordPress WordPress plugin deactivated trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Deactivated plugin trigger class
 */
class Deactivated extends PluginTrigger {

	/**
     * Constructor
     */
	public function __construct( ){

		parent::__construct( array(
			'slug' => 'wordpress/deactived_plugin',
			'name' => __( 'Plugin deactivated', 'notification' )
		) );

		$this->add_action( 'deactivated_plugin', 10, 2 );
		$this->set_group( __( 'WordPress', 'notification' ) );
		$this->set_description( __( 'Fires when plugin is deactivated', 'notification' ) );
	}

	/**
     * Trigger action
     *
     * @param  string $plugin_rel_path Plugin path.
     * @return void
     */
	public function action( $plugin_rel_path ) {

		$plugin_dir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin_rel_path;
		$plugin_data = get_plugin_data( $plugin_dir );
		$this->plugin = $plugin_data;
		$this->plugin_active_date_time = strtotime( 'now' );

	}

	/**
     * Registers attached merge tags
     *
     * @return void
     */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => 'plugin_deactive_date_time',
			'name' => __( 'Plugin deactivated date time', 'notification' ),
		) ) );
	}


}
