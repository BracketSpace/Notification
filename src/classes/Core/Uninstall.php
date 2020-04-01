<?php
/**
 * Uninstall class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Admin\Extensions;

/**
 * Uninstall class
 */
class Uninstall {

	/**
	 * Removing plugin data
	 *
	 * @since  6.0.5
	 * @return void
	 */
	public static function remove_plugin_data() {

		global $wpdb;

		$general_settings = get_option( 'notification_general' );

		$un = $general_settings['uninstallation'];

		// Remove notifications.
		if ( isset( $un['notifications'] ) && 'true' === $un['notifications'] ) {
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'notification'" ); // phpcs:ignore
		}

		// Remove settings.
		if ( isset( $un['settings'] ) && 'true' === $un['settings'] ) {

			$settings_config = get_option( '_notification_settings_config' );

			foreach ( $settings_config as $section_slug => $section ) {
				delete_option( 'notification_' . $section_slug );
				delete_site_option( 'notification_' . $section_slug );
			}

			delete_option( '_notification_settings_config' );
			delete_option( '_notification_settings_hash' );

		}

		// Remove licenses.
		if ( isset( $un['licenses'] ) && 'true' === $un['licenses'] ) {

			$extensions_class = new Extensions();

			$extensions_class->load_extensions();
			$premium_extensions = $extensions_class->premium_extensions;

			foreach ( $premium_extensions as $extension ) {
				$license = $extension['license'];
				if ( $license->is_valid() ) {
					$license->deactivate();
				}
			}

			delete_option( 'notification_licenses' );

		}

		// Remove other things.
		delete_option( 'notification_story_dismissed' );
		delete_option( 'notification_wizard_dismissed' );
		delete_option( 'notification_debug_log' );
		delete_option( 'notification_data_version' );
		delete_option( 'notification_db_version' );

	}

}
