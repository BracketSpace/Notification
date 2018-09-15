<?php
/**
 * WordPress plugin removed trigger.
 *
 * @package notification.
 */

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Removed plugin trigger class.
 */
class Removed extends PluginTrigger {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct( 'wordpress/plugin/removed', __( 'Plugin removed', 'notification' ) );

		$this->add_action( 'delete_plugin', 10, 2 );
		$this->set_group( __( 'Plugin', 'notification' ) );
		$this->set_description( __( 'Fires when plugin is deletion', 'notification' ) );

	}

	/**
	 * Trigger action.
	 *
	 * @param  string $plugin_rel_path Plugin path.
	 * @return mixed void or false if no notifications should be sent.
	 */
	public function action( $plugin_rel_path ) {

		$plugin_dir                    = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin_rel_path;
		$this->plugin                  = get_plugin_data( $plugin_dir );
		$this->plugin_delete_date_time = strtotime( 'now' );

	}

	/**
	 * Registers attached merge tags.
	 *
	 * @return void.
	 */
	public function merge_tags() {
		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => 'plugin_delete_date_time',
			'name' => __( 'Plugin deletion date and time', 'notification' ),
		) ) );
	}
}
