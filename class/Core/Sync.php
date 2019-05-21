<?php
/**
 * Synchronization
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Defaults\Adapter\WordPress;

/**
 * Sync class
 */
class Sync {

	/**
	 * Gets all Notifications from JSON files
	 *
	 * @since  6.0.0
	 * @return array
	 */
	public static function get_all_json() {

		if ( ! notification_is_syncing() ) {
			return [];
		}

		$path = untrailingslashit( notification_get_sync_path() );
		$dir  = opendir( $path );

		if ( ! $dir ) {
			return [];
		}

		$notifications = [];

		while ( false !== ( $file = readdir( $dir ) ) ) { // phpcs:ignore

			if ( pathinfo( $file, PATHINFO_EXTENSION ) !== 'json' ) {
				continue;
			}

			$json = file_get_contents( $path . '/' . $file ); // phpcs:ignore

			if ( empty( $json ) ) {
				continue;
			}

			$notifications[] = $json;

		}

		return $notifications;

	}

	/**
	 * Loads local JSON files
	 *
	 * @action notification/boot
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function load_local_json() {

		if ( ! notification_is_syncing() ) {
			return;
		}

		$notifications = self::get_all_json();

		foreach ( $notifications as $json ) {
			try {
				$adapter = notification_adapt_from( 'JSON', $json );
				$adapter->register_notification();
			} catch ( \Exception $e ) {
				// Do nothing.
				return;
			}
		}

	}

	/**
	 * Saves local JSON file
	 *
	 * @action notification/data/save/after
	 *
	 * @since  6.0.0
	 * @param Adapter\WordPress $wp_adapter WordPress adapter.
	 * @return void
	 */
	public static function save_local_json( $wp_adapter ) {

		if ( ! notification_is_syncing() ) {
			return;
		}

		$path = trailingslashit( notification_get_sync_path() );

		if ( ! is_dir( $path ) ) {
			return;
		}

		$file = $wp_adapter->get_hash() . '.json';
		$json = notification_swap_adapter( 'JSON', $wp_adapter )->save();

		file_put_contents( $path . '/' . $file, $json ); // phpcs:ignore

	}

	/**
	 * Deletes local JSON file
	 *
	 * @action delete_post
	 *
	 * @since  6.0.0
	 * @param  integer $post_id Deleted Post ID.
	 * @return void
	 */
	public function delete_local_json( $post_id ) {

		if ( ! notification_is_syncing() || 'notification' !== get_post_type( $post_id ) ) {
			return;
		}

		$adapter = notification_adapt_from( 'WordPress', $post_id );
		$path    = trailingslashit( notification_get_sync_path() );
		$file    = $adapter->get_hash() . '.json';

		if ( file_exists( $path . '/' . $file ) ) {
			unlink( $path . '/' . $file );
		}

	}

}
