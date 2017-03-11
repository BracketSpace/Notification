<?php
/**
 * Upgrade class
 */

namespace underDEV\Notification;

use underDEV\Utils\Singleton;

class Upgrade extends Singleton {

	/**
	 * Version of the database
	 * @var integer
	 */
	public $version = 1;

	/**
	 * Current version of the database
	 * @var integer
	 */
	public $current_version;

	public function __construct() {

		$this->current_version = get_option( 'notification_db_version', 0 );

		if ( version_compare( $this->current_version, $this->version, '<' ) ) {
			$this->update_db();
		}

	}

	/**
	 * Do all of needed DB updates
	 * @return void
	 */
	public function update_db() {

		while ( $this->current_version <= $this->version ) {

			$this->current_version++;

			if ( method_exists( $this, 'upgrade_to_version_' . $this->current_version ) ) {
				call_user_func( array( $this, 'upgrade_to_version_' . $this->current_version ) );
			}

		}

		$this->set_db_version( $this->version );

	}

	/**
	 * Set current DB version
	 * @param  integer $version version number
	 * @return void
	 */
	public function set_db_version( $version ) {

		update_option( 'notification_db_version', $version );

	}

	/**
	 * Upgrade to version 1
	 * - Change the `post_types_triggers` settings group to `enabled_triggers` in General section
	 * @return void
	 */
	private function upgrade_to_version_1() {

		$general_settings = get_option( 'notification_general' );
		$general_settings['enabled_triggers'] = $general_settings['post_types_triggers'];
		unset( $general_settings['post_types_triggers'] );
		update_option( 'notification_general', $general_settings );

	}

}
