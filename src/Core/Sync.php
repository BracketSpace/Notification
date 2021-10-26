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
	 * Sync path
	 *
	 * @var string|null
	 */
	protected static $sync_path;

	/**
	 * Gets all Notifications from JSON files
	 *
	 * @since  6.0.0
	 * @return array
	 */
	public static function get_all_json() {

		if ( ! self::is_syncing() ) {
			return [];
		}

		$path = untrailingslashit( (string) self::get_sync_path() );
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
	 * @action notification/init
	 *
	 * @since  6.0.0
	 * @return void
	 */
	public function load_local_json() {

		if ( ! self::is_syncing() ) {
			return;
		}

		$notifications = self::get_all_json();

		foreach ( $notifications as $json ) {
			try {
				/**
				 * JSON Adapter
				 *
				 * @var \BracketSpace\Notification\Defaults\Adapter\JSON
				 */
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
	 * @param  WordPress $wp_adapter WordPress adapter.
	 * @return void
	 */
	public static function save_local_json( $wp_adapter ) {

		if ( ! self::is_syncing() ) {
			return;
		}

		$path = trailingslashit( (string) self::get_sync_path() );

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

		if ( ! self::is_syncing() || 'notification' !== get_post_type( $post_id ) ) {
			return;
		}

		$adapter = notification_adapt_from( 'WordPress', $post_id );
		$path    = trailingslashit( (string) self::get_sync_path() );
		$file    = $adapter->get_hash() . '.json';

		if ( file_exists( $path . '/' . $file ) ) {
			unlink( $path . '/' . $file );
		}

	}

	/**
	 * Enables the notification syncing
	 * By default path used is current theme's `notifiations` dir.
	 *
	 * @since  8.0.0
	 * @throws \Exception If provided path is not a directory.
	 * @param  string $path full json directory path or null to use default.
	 * @return void
	 */
	public static function enable( string $path = null ) {
		if ( ! $path ) {
			$path = trailingslashit( get_stylesheet_directory() ) . 'notifications';
		}

		if ( ! file_exists( $path ) ) {
			mkdir( $path );
		}

		if ( ! is_dir( $path ) ) {
			throw new \Exception( 'Synchronization path must be a directory.' );
		}

		if ( self::is_syncing() ) {
			throw new \Exception( sprintf( 'Synchronization has been already enabled and it\'s syncing to: %s', self::get_sync_path() ) );
		}

		if ( ! file_exists( trailingslashit( $path ) . 'index.php' ) ) {
			file_put_contents( trailingslashit( $path ) . 'index.php', '<?php' . "\r\n" . '// Keep this file here.' . "\r\n" ); // phpcs:ignore
		}

		static::$sync_path = $path;
	}

	/**
	 * Disables the synchronization.
	 *
	 * @since  8.0.0
	 * @return void
	 */
	public static function disable() {
		static::$sync_path = null;
	}

	/**
	 * Gets the synchronization path.
	 *
	 * @since  8.0.0
	 * @return string|null
	 */
	public static function get_sync_path() {
		return static::$sync_path;
	}

	/**
	 * Gets the synchronization path.
	 *
	 * @since  8.0.0
	 * @return bool
	 */
	public static function is_syncing() : bool {
		return null !== static::$sync_path;
	}

}
