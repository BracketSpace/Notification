<?php
/**
 * WordPress plugin updated trigger.
 *
 * @package notification.
 */

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Updated plugin trigger class.
 */
class Updated extends PluginTrigger {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct( 'wordpress/plugin/updated', __( 'Plugin updated', 'notification' ) );

		$this->add_action( 'upgrader_process_complete', 1000, 2 );

		$this->set_group( __( 'Plugin', 'notification' ) );
		$this->set_description( __( 'Fires when plugin is updated', 'notification' ) );

	}

	/**
	 * Trigger action.
	 *
	 * @param  Plugin_Upgrader $upgrader Plugin_Upgrader class.
	 * @param  array           $data     Update data information.
	 * @return mixed                     Void or false if no notifications should be sent.
	 */
	public function action( $upgrader, $data ) {

		if ( ! isset( $data['type'], $data['action'] ) || 'plugin' !== $data['type'] || 'update' !== $data['action'] ) {
			return false;
		}

		$this->previous_version        = $upgrader->skin->plugin_info['Version'];
		$plugin_dir                    = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $upgrader->plugin_info();
		$this->plugin                  = get_plugin_data( $plugin_dir, false );
		$this->plugin_update_date_time = current_time( 'timestamp' );

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'plugin_update_date_time',
			'name' => __( 'Plugin update date and time', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'plugin_previous_version',
			'name'        => __( 'Plugin previous version', 'notification' ),
			'description' => __( '1.0.0', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->previous_version;
			},
			'group'       => __( 'Plugin', 'notification' ),
		] ) );

	}
}
