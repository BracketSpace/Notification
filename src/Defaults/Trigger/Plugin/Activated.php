<?php
/**
 * WordPress plugin activated trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Plugin;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Activated plugin trigger class
 */
class Activated extends PluginTrigger {

	/**
	 * Plugin activation date and time
	 *
	 * @var string
	 */
	public $plugin_activation_date_time;

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'plugin/activated', __( 'Plugin activated', 'notification' ) );

		$this->add_action( 'activated_plugin', 1000 );

		$this->set_group( __( 'Plugin', 'notification' ) );
		$this->set_description( __( 'Fires when plugin is activated', 'notification' ) );

	}

	/**
	 * Trigger action
	 *
	 * @param  string $plugin_rel_path Plugin path.
	 * @return void
	 */
	public function context( $plugin_rel_path ) {

		$plugin_dir                        = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin_rel_path;
		$this->plugin                      = get_plugin_data( $plugin_dir, false );
		$this->plugin_activation_date_time = time();

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'plugin_activation_date_time',
			'name' => __( 'Plugin activation date and time', 'notification' ),
		] ) );

	}

}
