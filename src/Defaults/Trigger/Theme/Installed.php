<?php
/**
 * WordPress theme installed trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Theme;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Installed theme trigger class
 */
class Installed extends ThemeTrigger {

	/**
	 * Theme installation date and time
	 *
	 * @var string
	 */
	public $theme_installation_date_time;

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'theme/installed', __( 'Theme installed', 'notification' ) );

		$this->add_action( 'upgrader_process_complete', 1000, 2 );

		$this->set_group( __( 'Theme', 'notification' ) );
		$this->set_description( __( 'Fires when theme is installed', 'notification' ) );

	}

	/**
	 * Trigger action.
	 *
	 * @param  \Theme_Upgrader $upgrader Theme_Upgrader class.
	 * @param  array           $data     Update data information.
	 * @return mixed                     Void or false if no notifications should be sent.
	 */
	public function context( $upgrader, $data ) {

		if ( ! isset( $data['type'], $data['action'] ) || 'theme' !== $data['type'] || 'install' !== $data['action'] ) {
			return false;
		}

		$theme = $upgrader->theme_info();

		if ( false === $theme ) {
			return false;
		}

		$this->theme = $theme;

		$this->theme_installation_date_time = time();

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'theme_installation_date_time',
			'name' => __( 'Theme installation date and time', 'notification' ),
		] ) );

	}

}
