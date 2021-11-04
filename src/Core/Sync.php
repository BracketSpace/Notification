<?php
/**
 * Synchronization
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Defaults\Adapter\WordPress;
use BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem;

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
	 * @return array<int,string>
	 */
	public static function get_all_json() {
		if ( ! self::is_syncing() ) {
			return [];
		}

		$fs = static::get_sync_fs();

		$notifications = [];

		if ( ! $fs ) {
			return $notifications;
		}

		foreach ( (array) $fs->dirlist( '/' ) as $filename => $file ) {
			if ( 1 !== preg_match( '/.*\.json/', $filename ) ) {
				continue;
			}

			$json = $fs->get_contents( $filename );

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
	 * @action notification/init 100
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

				if ( $adapter->is_enabled() ) {
					$adapter->register_notification();
				}
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

		$fs = static::get_sync_fs();

		if ( ! $fs ) {
			return;
		}

		$file = $wp_adapter->get_hash() . '.json';
		$json = notification_swap_adapter( 'JSON', $wp_adapter )->save();

		$fs->put_contents( $file, $json );
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

		$fs = static::get_sync_fs();

		if ( ! $fs ) {
			return;
		}

		$adapter = notification_adapt_from( 'WordPress', $post_id );
		$file    = $adapter->get_hash() . '.json';

		if ( $fs->exists( $file ) ) {
			$fs->delete( $file );
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

		if ( self::is_syncing() ) {
			throw new \Exception( sprintf( 'Synchronization has been already enabled and it\'s syncing to: %s', self::get_sync_path() ) );
		}

		static::$sync_path = $path;

		$fs = static::get_sync_fs();

		if ( ! $fs ) {
			return;
		}

		if ( ! $fs->exists( '' ) || ! $fs->is_dir( '' ) ) {
			$fs->mkdir( '' );
		}

		if ( ! $fs->exists( 'index.php' ) ) {
			$fs->touch( 'index.php' );
			$fs->put_contents( 'index.php', '<?php' . "\r\n" . '// Keep this file here.' . "\r\n" );
		}
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
	 * Gets the sync dir filesystem.
	 *
	 * @since  8.0.2
	 * @return Filesystem|null
	 */
	public static function get_sync_fs() {
		if ( ! static::is_syncing() ) {
			return null;
		}

		return new Filesystem( (string) static::get_sync_path() );
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
