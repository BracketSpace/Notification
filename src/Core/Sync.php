<?php

/**
 * Synchronization
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;
use BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem;
use BracketSpace\Notification\Integration\WordPressIntegration;
use function BracketSpace\Notification\addNotification;

/**
 * Sync class
 */
class Sync
{
	use Casegnostic;

	/**
	 * Sync path
	 *
	 * @var string|null
	 */
	protected static $syncPath;

	/**
	 * Gets all Notifications from JSON files
	 *
	 * @return array<int,string>
	 * @since  6.0.0
	 */
	public static function getAllJson()
	{
		if (!self::isSyncing()) {
			return [];
		}

		$fs = static::getSyncFs();

		$notifications = [];

		if (!$fs) {
			return $notifications;
		}

		foreach ((array)$fs->dirlist('/') as $filename => $file) {
			if (preg_match('/.*\.json/', (string)$filename) !== 1) {
				continue;
			}

			$json = $fs->get_contents((string)$filename);

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
	 * @return void
	 * @since  6.0.0
	 */
	public function loadLocalJson()
	{
		if (!self::isSyncing()) {
			return;
		}

		$notifications = self::getAllJson();

		foreach ($notifications as $json) {
			try {
				$notification = Notification::from('json', $json);

				if ($notification->isEnabled()) {
					addNotification($notification);
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
	 * @action notification/data/saved
	 *
	 * @since  6.0.0
	 * @param Notification $notification Notification.
	 * @return void
	 */
	public static function saveLocalJson($notification)
	{
		if (!self::isSyncing()) {
			return;
		}

		$fs = static::getSyncFs();

		if (!$fs) {
			return;
		}

		$file = $notification->getHash() . '.json';

		$fs->put_contents($file, $notification->to('json'));
	}

	/**
	 * Deletes local JSON file
	 *
	 * @action delete_post
	 *
	 * @param int $postId Deleted Post ID.
	 * @return void
	 * @since  6.0.0
	 */
	public function deleteLocalJson($postId)
	{
		if (!self::isSyncing() || get_post_type($postId) !== 'notification') {
			return;
		}

		$fs = static::getSyncFs();

		if (!$fs) {
			return;
		}

		$notification = WordPressIntegration::postToNotification($postId);

		if (! $notification instanceof Notification) {
			return;
		}

		$file = $notification->getHash() . '.json';

		if (!$fs->exists($file)) {
			return;
		}

		$fs->delete($file);
	}

	/**
	 * Enables the notification syncing
	 * By default path used is current theme's `notifications` dir.
	 *
	 * @param string $path full json directory path or null to use default.
	 * @return void
	 * @throws \Exception If provided path is not a directory.
	 * @since  8.0.0
	 */
	//phpcs:ignore SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilitySymbolRequired
	public static function enable(string $path = null)
	{
		if (!$path) {
			$path = trailingslashit(get_stylesheet_directory()) . 'notifications';
		}

		if (self::isSyncing()) {
			throw new \Exception(
				sprintf(
					'Synchronization has been already enabled and it\'s syncing to: %s',
					self::getSyncPath()
				)
			);
		}

		static::$syncPath = $path;

		$fs = static::getSyncFs();

		if (!$fs) {
			return;
		}

		if (!$fs->exists('') || !$fs->is_dir('')) {
			$fs->mkdir('');
		}

		if ($fs->exists('index.php')) {
			return;
		}

		$fs->touch('index.php');
		$fs->put_contents(
			'index.php',
			'<?php' . "\r\n" . '// Keep this file here.' . "\r\n"
		);
	}

	/**
	 * Disables the synchronization.
	 *
	 * @return void
	 * @since  8.0.0
	 */
	public static function disable()
	{
		static::$syncPath = null;
	}

	/**
	 * Gets the synchronization path.
	 *
	 * @return string|null
	 * @since  8.0.0
	 */
	public static function getSyncPath()
	{
		return static::$syncPath;
	}

	/**
	 * Gets the sync dir filesystem.
	 *
	 * @return \BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem|null
	 * @since  8.0.2
	 */
	public static function getSyncFs()
	{
		if (!static::isSyncing()) {
			return null;
		}

		return new Filesystem((string)static::getSyncPath());
	}

	/**
	 * Gets the synchronization path.
	 *
	 * @return bool
	 * @since  8.0.0
	 */
	public static function isSyncing(): bool
	{
		return static::$syncPath !== null;
	}
}
