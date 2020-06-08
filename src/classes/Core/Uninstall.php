<?php
/**
 * Uninstall class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

require_once __DIR__ . '/../Admin/Extensions.php';

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

		$settings = get_option( '_transient_notification_settings_config' );

		$uninstallation_settings = $settings['general']['uninstallation'];

		// Remove notifications.
		if ( isset( $uninstallation_settings['notifications'] ) && 'true' === $uninstallation_settings['notifications'] ) {
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'notification'" ); // phpcs:ignore
		}

		// Remove settings.
		if ( isset( $uninstallation_settings['settings'] ) && 'true' === $uninstallation_settings['settings'] ) {

			foreach ( $settings as $section_slug => $section ) {
				delete_option( 'notification_' . $section_slug );
				delete_site_option( 'notification_' . $section_slug );
			}

			delete_option( '_transient_notification_settings_config' );
			delete_option( '_notification_settings_hash' );

		}

		// Remove licenses.
		if ( isset( $uninstallation_settings['licenses'] ) && 'true' === $uninstallation_settings['licenses'] ) {

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

		// Remove logs table.
		$logs_table = $wpdb->prefix . 'notification_logs';
		$wpdb->query( "DROP TABLE IF EXISTS ${logs_table}"  ); // phpcs:ignore

		// Remove other things.
		delete_option( 'notification_story_dismissed' );
		delete_option( 'notification_wizard_dismissed' );
		delete_option( 'notification_debug_log' );
		delete_option( 'notification_data_version' );
		delete_option( 'notification_db_version' );

	}

}
