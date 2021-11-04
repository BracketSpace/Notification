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
	 * Theme update date and time
	 *
	 * @var string
	 */
	public $theme_update_date_time;

	/**
	 * Theme previous version
	 *
	 * @var string
	 */
	public $theme_previous_version;

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct( 'theme/updated', __( 'Theme updated', 'notification' ) );

		$this->add_action( 'upgrader_process_complete', 1000, 2 );

		$this->set_group( __( 'Theme', 'notification' ) );
		$this->set_description( __( 'Fires when theme is updated', 'notification' ) );

	}

	/**
	 * Trigger action.
	 *
	 * @param  \Theme_Upgrader $upgrader Theme_Upgrader class.
	 * @param  array           $data     Update data information.
	 * @return mixed                     Void or false if no notifications should be sent.
	 */
	public function context( $upgrader, $data ) {

		if ( ! isset( $data['type'], $data['action'] ) || 'theme' !== $data['type'] || 'update' !== $data['action'] ) {
			return false;
		}

		$theme = $upgrader->theme_info();

		if ( false === $theme ) {
			return false;
		}

		$this->theme = $theme;

		$this->theme_update_date_time = time();
		$this->theme_previous_version = ( ! property_exists( $upgrader->skin, 'theme_info' ) || null === $upgrader->skin->theme_info ) ? __( 'NA' ) : $upgrader->skin->theme_info->get( 'Version' );

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\StringTag( [
			'slug'        => 'theme_previous_version',
			'name'        => __( 'Theme previous version', 'notification' ),
			'description' => __( '1.0.0', 'notification' ),
			'example'     => true,
			'resolver'    => function ( $trigger ) {
				return $trigger->theme_previous_version;
			},
			'group'       => __( 'Theme', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => 'theme_update_date_time',
			'name' => __( 'Theme update date and time', 'notification' ),
		] ) );

	}

}
