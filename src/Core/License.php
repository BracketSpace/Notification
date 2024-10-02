<?php

/**
 * License class
 * Used by paid extensions to save and retrieve license from database
 * License is used to provide the updates
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\ErrorHandler;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;

/**
 * License class
 */
class License
{
	/**
	 * Extension data
	 *
	 * @var array<mixed>
	 */
	protected $extension;

	/**
	 * License storage key
	 *
	 * @var string
	 */
	protected $licenseStorage = 'notification_licenses';

	/**
	 * Class constructor
	 *
	 * @param array<mixed> $extension extension data.
	 * @since 5.1.0
	 */
	public function __construct(array $extension)
	{
		$this->extension = $extension;
	}

	/**
	 * Gets all licenses from database
	 *
	 * @return array<mixed> licenses
	 * @since  5.1.0
	 */
	public function getLicenses()
	{
		return get_option($this->licenseStorage, []);
	}

	/**
	 * Gets single license info
	 *
	 * @return mixed license data or false
	 * @since  5.1.0
	 */
	public function get()
	{
		$driver = new CacheDriver\ObjectCache('notification_license');
		$cache = new Cache($driver, $this->extension['slug']);

		return $cache->collect(
			function () {
				$licenses = $this->getLicenses();
				$license = false;

				if (isset($licenses[$this->extension['slug']])) {
					$license = $licenses[$this->extension['slug']];
				}

				return $license;
			}
		);
	}

	/**
	 * Checks if license is valid
	 *
	 * @return bool
	 * @since  5.1.0
	 */
	public function isValid()
	{
		$licenseData = $this->get();

		if (empty($licenseData)) {
			return false;
		}

		$driver = new CacheDriver\Transient(ErrorHandler::debugEnabled() ? 60 : DAY_IN_SECONDS);
		$cache = new Cache($driver, sprintf('notification_license_check_%s', $this->extension['slug']));

		return $cache->collect(
			function () use ($licenseData) {
				$licenseCheck = $this->check($licenseData->licenseKey);

				if (is_wp_error($licenseCheck)) {
					return $licenseData->license === 'valid';
				}

				$licenseCheck->licenseKey = $licenseData->licenseKey;
				$licenseData = $licenseCheck;
				$this->save($licenseData);

				return $licenseData->license === 'valid';
			}
		);
	}

	/**
	 * Gets the license key
	 *
	 * @return string
	 * @since  7.1.1
	 */
	public function getKey()
	{
		$licenseData = $this->get();
		return $licenseData->licenseKey ?? '';
	}

	/**
	 * Saves single license info
	 *
	 * @param object $licenseData license data from API.
	 * @return void
	 * @since  5.1.0
	 */
	public function save($licenseData)
	{
		$driver = new CacheDriver\ObjectCache('notification_license');
		$cache = new Cache($driver, $this->extension['slug']);
		$cache->set($licenseData);

		$licenses = $this->getLicenses();
		$licenses[$this->extension['slug']] = $licenseData;

		update_option($this->licenseStorage, $licenses);
	}

	/**
	 * Removes single license from database
	 *
	 * @return void
	 * @since  5.1.0
	 */
	public function remove()
	{
		$driver = new CacheDriver\ObjectCache('notification_license');
		$cache = new Cache($driver, $this->extension['slug']);
		$cache->delete();

		$licenses = $this->getLicenses();
		if (isset($licenses[$this->extension['slug']])) {
			unset($licenses[$this->extension['slug']]);
		}

		update_option($this->licenseStorage, $licenses);
	}

	/**
	 * Activates the license
	 *
	 * @param string $licenseKey license key.
	 * @return mixed               WP_Error or License data
	 * @since  5.1.0
	 */
	public function activate($licenseKey = '')
	{
		$licenseKey = trim($licenseKey);
		$error = false;

		/** @var string $itemName */
		$itemName = $this->extension['edd']['item_name'];

		// Call the custom API.
		$response = wp_remote_post(
			$this->extension['edd']['store_url'],
			[
				'timeout' => 15,
				'body' => [
					'edd_action' => 'activate_license',
					'license' => $licenseKey,
					'item_name' => rawurlencode($itemName),
					'url' => home_url(),
				],
			]
		);

		// Make sure the response came back okay.
		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
			return new \WP_Error(
				'notification_license_error',
				'http-error'
			);
		}

		$licenseData = json_decode(wp_remote_retrieve_body($response));

		if ($licenseData->success === false) {
			return new \WP_Error(
				'notification_license_error',
				$licenseData->error,
				$licenseData
			);
		}

		$licenseData->licenseKey = $licenseKey;
		$this->save($licenseData);

		$driver = new CacheDriver\ObjectCache('notification_license');
		$cache = new Cache($driver, $this->extension['slug']);
		$cache->delete();

		$driver = new CacheDriver\Transient(ErrorHandler::debugEnabled() ? 60 : DAY_IN_SECONDS);
		$cache = new Cache($driver, sprintf('notification_license_check_%s', $this->extension['slug']));
		$cache->delete();

		return $licenseData;
	}

	/**
	 * Deactivates the license
	 *
	 * @return mixed WP_Error or License data
	 * @since  5.1.0
	 */
	public function deactivate()
	{
		$licenseData = $this->get();
		$error = false;

		/** @var string $itemName */
		$itemName = $this->extension['edd']['item_name'];

		// Call the custom API.
		$response = wp_remote_post(
			$this->extension['edd']['store_url'],
			[
				'timeout' => 15,
				'body' => [
					'edd_action' => 'deactivate_license',
					'license' => trim((string)$licenseData->licenseKey),
					'item_name' => rawurlencode($itemName),
					'url' => home_url(),
				],
			]
		);

		// Make sure the response came back okay.
		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
			return new \WP_Error(
				'notification_license_error',
				'http-error'
			);
		}

		$licenseData = json_decode(wp_remote_retrieve_body($response));

		if (!in_array($licenseData->license, ['deactivated', 'failed'], true)) {
			return new \WP_Error(
				'notification_license_error',
				'deactivation-error'
			);
		}

		$this->remove();

		$driver = new CacheDriver\ObjectCache('notification_license');
		$cache = new Cache($driver, $this->extension['slug']);
		$cache->delete();

		$driver = new CacheDriver\Transient(ErrorHandler::debugEnabled() ? 60 : DAY_IN_SECONDS);
		$cache = new Cache($driver, sprintf('notification_license_check_%s', $this->extension['slug']));
		$cache->delete();

		return $licenseData;
	}

	/**
	 * Checks the license
	 *
	 * @param string $licenseKey license key.
	 * @return object              WP_Error or license object
	 * @since  5.1.0
	 */
	public function check($licenseKey = '')
	{
		$licenseKey = trim((string)$licenseKey);
		$error = false;

		/** @var string $itemName */
		$itemName = $this->extension['edd']['item_name'];

		// Call the custom API.
		$response = wp_remote_post(
			$this->extension['edd']['store_url'],
			[
				'timeout' => 15,
				'body' => [
					'edd_action' => 'check_license',
					'license' => $licenseKey,
					'item_name' => rawurlencode($itemName),
					'url' => home_url(),
				],
			]
		);

		// Make sure the response came back okay.
		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
			return new \WP_Error(
				'notification_license_error',
				'http-error'
			);
		}

		$licenseData = json_decode(wp_remote_retrieve_body($response));
		$licenseData->licenseKey = $licenseKey;

		return $licenseData;
	}
}
