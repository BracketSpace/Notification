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
	public static function getAllJson()
	{
		if (! self::isSyncing()) {
			return [];
		}

		$fs = static::getSyncFs();

		$notifications = [];

		if (! $fs) {
			return $notifications;
		}

		foreach ((array)$fs->dirlist('/') as $filename => $file) {
			if (preg_match('/.*\.json/', $filename) !== 1) {
				continue;
			}

			$json = $fs->getContents($filename);

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
	public function loadLocalJson()
	{
		if (! self::isSyncing()) {
			return;
		}

		$notifications = self::getAllJson();

		foreach ($notifications as $json) {
			try {
				/**
				 * JSON Adapter
				 *
				 * @var \BracketSpace\Notification\Defaults\Adapter\JSON
				 */
				$adapter = notification_adapt_from('JSON', $json);

				if ($adapter->isEnabled()) {
					$adapter->registerNotification();
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
	public static function saveLocalJson( $wpAdapter )
	{
		if (! self::isSyncing()) {
			return;
		}

		$fs = static::getSyncFs();

		if (! $fs) {
			return;
		}

		$file = $wpAdapter->getHash() . '.json';
		$json = notification_swap_adapter('JSON', $wpAdapter)->save();

		$fs->putContents($file, $json);
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
	public function deleteLocalJson( $postId )
	{
		if (! self::isSyncing() || get_post_type($postId) !== 'notification') {
			return;
		}

		$fs = static::getSyncFs();

		if (! $fs) {
			return;
		}

		$adapter = notification_adapt_from('WordPress', $postId);
		$file = $adapter->getHash() . '.json';

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

		if (self::isSyncing()) {
			throw new \Exception(sprintf('Synchronization has been already enabled and it\'s syncing to: %s', self::getSyncPath()));
		}

		static::$syncPath = $path;

		$fs = static::getSyncFs();

		if (! $fs) {
			return;
		}

		if (! $fs->exists('') || ! $fs->isDir('')) {
			$fs->mkdir('');
		}

		if ($fs->exists('index.php')) {
			return;
		}

		$fs->touch('index.php');
		$fs->putContents('index.php', '<?php' . "\r\n" . '// Keep this file here.' . "\r\n");
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
	public static function getSyncPath()
	{
		return static::$syncPath;
	}

	/**
	 * Gets the sync dir filesystem.
	 *
	 * @since  8.0.2
	 * @return \BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem|null
	 */
	public static function getSyncFs()
	{
		if (! static::isSyncing()) {
			return null;
		}

		return new Filesystem((string)static::getSyncPath());
	}

	/**
	 * Gets the synchronization path.
	 *
	 * @since  8.0.0
	 * @return bool
	 */
	public static function isSyncing(): bool
	{
		return static::$syncPath !== null;
	}
}
