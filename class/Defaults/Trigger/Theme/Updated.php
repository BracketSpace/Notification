<?php
/**
 * WordPress theme updated trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Theme;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Updated theme trigger class
 */
class Updated extends ThemeTrigger {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'wordpress/theme/updated', __( 'Theme updated', 'notification' ) );

		$this->add_action( 'upgrader_process_complete', 1000, 2 );

		$this->set_group( __( 'Theme', 'notification' ) );
		$this->set_description( __( 'Fires when theme is updated', 'notification' ) );

	}

	/**
	 * Trigger action.
	 *
	 * @param  Theme_Upgrader $upgrader Theme_Upgrader class.
	 * @param  array          $data     Update data information.
	 * @return mixed                    Void or false if no notifications should be sent.
	 */
	public function action( $upgrader, $data ) {

		if ( ! isset( $data['type'], $data['action'] ) || 'theme' !== $data['type'] || 'update' !== $data['action'] ) {
			return false;
		}

		$this->theme                  = $upgrader->theme_info();
		$this->theme_previous_version = $upgrader->skin->theme_info->get( 'Version' );
		$this->theme_update_date_time = current_time( 'timestamp' );

		if ( false === $this->theme ) {
			return false;
		}

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\StringTag( array(
			'slug'        => 'theme_previous_version',
			'name'        => __( 'Theme previous version', 'notification' ),
			'description' => __( '1.0.0', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->theme_previous_version;
			},
		) ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => 'theme_update_date_time',
			'name' => __( 'Theme update date and time', 'notification' ),
		) ) );

	}

}
