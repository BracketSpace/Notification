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
	 * Plugin installation date and time
	 *
	 * @var string
	 */
	public $plugin_installation_date_time;

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct( 'plugin/installed', __( 'Plugin installed', 'notification' ) );

		$this->add_action( 'upgrader_process_complete', 1000, 2 );

		$this->set_group( __( 'Plugin', 'notification' ) );
		$this->set_description( __( 'Fires when plugin is installed', 'notification' ) );

	}

	/**
	 * Trigger action.
	 *
	 * @param  \Plugin_Upgrader $upgrader Plugin_Upgrader class.
	 * @param  array            $data     Update data information.
	 * @return mixed                      Void or false if no notifications should be sent.
	 */
	public function context( $upgrader, $data ) {

		if ( ! isset( $data['type'], $data['action'] ) || 'plugin' !== $data['type'] || 'install' !== $data['action'] ) {
			return false;
		}

		$plugin_dir                          = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $upgrader->plugin_info();
		$this->plugin                        = get_plugin_data( $plugin_dir, false );
		$this->plugin_installation_date_time = time();

	}

	/**
	 * Registers attached merge tags.
	 *
	 * @return void.
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'plugin_installation_date_time',
			'name' => __( 'Plugin installation date and time', 'notification' ),
		] ) );

	}

}
