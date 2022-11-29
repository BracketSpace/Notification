<?php

/**
 * Synchronization
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem;

/**
 * Sync class
 */
class Sync
{

	/**
	 * Sync path
	 *
	 * @var string|null
	 */
	protected static $syncPath;

	/**
	 * Gets all Notifications from JSON files
	 *
	 * @since  6.0.0
	 * @return array<int,string>
	 */
	public static function get_all_json()
	{
		if (! self::is_syncing()) {
			return [];
		}

		$fs = static::get_sync_fs();

		$notifications = [];

		if (! $fs) {
			return $notifications;
		}

		foreach ((array)$fs->dirlist('/') as $filename => $file) {
			if (preg_match('/.*\.json/', $filename) !== 1) {
				continue;
			}

			$json = $fs->get_contents($filename);

			if (empty($json)) {
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
	public function load_local_json()
	{
		if (! self::is_syncing()) {
			return;
		}

		$notifications = self::get_all_json();

		foreach ($notifications as $json) {
			try {
				/**
				 * JSON Adapter
				 *
				 * @var \BracketSpace\Notification\Defaults\Adapter\JSON
				 */
				$adapter = notification_adapt_from('JSON', $json);

				if ($adapter->is_enabled()) {
					$adapter->register_notification();
				}
			} catch (\Throwable $e) {
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
	 * @param \BracketSpace\Notification\Defaults\Adapter\WordPress $wpAdapter WordPress adapter.
	 * @return void
	 */
	public static function save_local_json( $wpAdapter )
	{
		if (! self::is_syncing()) {
			return;
		}

		$fs = static::get_sync_fs();

		if (! $fs) {
			return;
		}

		$file = $wpAdapter->get_hash() . '.json';
		$json = notification_swap_adapter('JSON', $wpAdapter)->save();

		$fs->put_contents($file, $json);
	}

	/**
	 * Deletes local JSON file
	 *
	 * @action delete_post
	 *
	 * @since  6.0.0
	 * @param int $postId Deleted Post ID.
	 * @return void
	 */
	public function delete_local_json( $postId )
	{
		if (! self::is_syncing() || get_post_type($postId) !== 'notification') {
			return;
		}

		$fs = static::get_sync_fs();

		if (! $fs) {
			return;
		}

		$adapter = notification_adapt_from('WordPress', $postId);
		$file = $adapter->get_hash() . '.json';

		if (!$fs->exists($file)) {
			return;
		}

		$fs->delete($file);
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
	public static function enable( ?string $path = null )
	{
		if (! $path) {
			$path = trailingslashit(get_stylesheet_directory()) . 'notifications';
		}

		if (self::is_syncing()) {
			throw new \Exception(sprintf('Synchronization has been already enabled and it\'s syncing to: %s', self::get_sync_path()));
		}

		static::$syncPath = $path;

		$fs = static::get_sync_fs();

		if (! $fs) {
			return;
		}

		if (! $fs->exists('') || ! $fs->is_dir('')) {
			$fs->mkdir('');
		}

		if ($fs->exists('index.php')) {
			return;
		}

		$fs->touch('index.php');
		$fs->put_contents('index.php', '<?php' . "\r\n" . '// Keep this file here.' . "\r\n");
	}

	/**
	 * Disables the synchronization.
	 *
	 * @since  8.0.0
	 * @return void
	 */
	public static function disable()
	{
		static::$syncPath = null;
	}

	/**
	 * Gets the synchronization path.
	 *
	 * @since  8.0.0
	 * @return string|null
	 */
	public static function get_sync_path()
	{
		return static::$syncPath;
	}

	/**
	 * Gets the sync dir filesystem.
	 *
	 * @since  8.0.2
	 * @return \BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem|null
	 */
	public static function get_sync_fs()
	{
		if (! static::is_syncing()) {
			return null;
		}

		return new Filesystem((string)static::get_sync_path());
	}

	/**
	 * Gets the synchronization path.
	 *
	 * @since  8.0.0
	 * @return bool
	 */
	public static function is_syncing(): bool
	{
		return static::$syncPath !== null;
	}
}
