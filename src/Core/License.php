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
	 * License server URL
	 */
	public const STORE_URL = 'https://lic.bracketspace.com';

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
	public function getLicenses(): array
	{
		$licenses = get_option($this->licenseStorage, []);
		return is_array($licenses) ? $licenses : [];
	}

	/**
	 * Gets single license info
	 *
	 * @return mixed license data or false
	 * @since  5.1.0
	 */
	public function get()
	{
		$driver = new CacheDriver\ObjectCache('notification_license/v2');
		$slug = is_scalar($this->extension['slug'] ?? null) ? (string)$this->extension['slug'] : '';
		$cache = new Cache($driver, $slug);

		return $cache->collect(
			function () {
				$licenses = $this->getLicenses();
				$license = new \stdClass();

				if (! isset($licenses[$this->extension['slug']])) {
					return false;
				}

				foreach ((array)$licenses[$this->extension['slug']] as $key => $value) {
					$keyMapped = lcfirst(str_replace('_', '', ucwords((string)$key, '_')));
					$license->$keyMapped = $value;
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
	public function isValid(): bool
	{
		$licenseData = $this->get();

		if (empty($licenseData)) {
			return false;
		}

		$driver = new CacheDriver\Transient(ErrorHandler::debugEnabled() ? 60 : DAY_IN_SECONDS);
		$slug = is_scalar($this->extension['slug'] ?? null) ? (string)$this->extension['slug'] : '';
		$cache = new Cache($driver, sprintf('notification_license_check_%s', $slug));

		return $cache->collect(
			function () use ($licenseData) {
				// Ensure licenseData is object with required properties
				if (!is_object($licenseData) || !property_exists($licenseData, 'licenseKey')) {
					return false;
				}

				$licenseCheck = $this->check($licenseData->licenseKey);

				if (is_wp_error($licenseCheck)) {
					return is_object($licenseData) && property_exists($licenseData, 'license')
						? $licenseData->license === 'valid' : false;
				}

				// Always update stored license data if API returned different status
				if (
					is_object($licenseCheck) && is_object($licenseData) &&
					property_exists($licenseCheck, 'license') && property_exists($licenseData, 'license') &&
					property_exists($licenseCheck, 'expires') && property_exists($licenseData, 'expires') &&
					($licenseCheck->license !== $licenseData->license ||
					$licenseCheck->expires !== $licenseData->expires)
				) {
					$licenseCheck->licenseKey = $licenseData->licenseKey;
					$this->save($licenseCheck);
					$licenseData = $licenseCheck;
				} else {
					if (
						is_object($licenseCheck) && is_object($licenseData) &&
						property_exists($licenseData, 'licenseKey')
					) {
						$licenseCheck->licenseKey = $licenseData->licenseKey;
						$licenseData = $licenseCheck;
						$this->save($licenseData);
					}
				}

				return is_object($licenseData) && property_exists($licenseData, 'license')
					? $licenseData->license === 'valid' : false;
			}
		);
	}

	/**
	 * Logs license API errors to the error log
	 *
	 * @param string $action EDD action that failed.
	 * @param mixed $response WP HTTP response or WP_Error.
	 * @return void
	 */
	private function logApiError(string $action, $response): void
	{
		$slug = $this->extension['slug'] ?? 'unknown';

		if (is_wp_error($response)) {
			error_log( // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
				sprintf(
					'[Notification] License %s failed for "%s": %s',
					$action,
					$slug,
					$response->get_error_message()
				)
			);
			return;
		}

		error_log( // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
			sprintf(
				'[Notification] License %s failed for "%s": HTTP %d — %s',
				$action,
				$slug,
				wp_remote_retrieve_response_code($response),
				wp_remote_retrieve_body($response)
			)
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
		$driver = new CacheDriver\ObjectCache('notification_license/v2');
		$slug = is_scalar($this->extension['slug'] ?? null) ? (string)$this->extension['slug'] : '';
		$cache = new Cache($driver, $slug);
		$cache->set($licenseData);

		$licenses = $this->getLicenses();
		if ($slug !== '') {
			$licenses[$slug] = $licenseData;
		}

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
		$driver = new CacheDriver\ObjectCache('notification_license/v2');
		$slug = is_scalar($this->extension['slug'] ?? null) ? (string)$this->extension['slug'] : '';
		$cache = new Cache($driver, $slug);
		$cache->delete();

		$licenses = $this->getLicenses();
		if ($slug !== '' && isset($licenses[$slug])) {
			unset($licenses[$slug]);
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

		// Ensure we have valid EDD configuration
		if (!is_array($this->extension) || !isset($this->extension['edd']) || !is_array($this->extension['edd'])) {
			return new \WP_Error('invalid_config', 'Invalid extension configuration');
		}

		$eddConfig = $this->extension['edd'];
		if (!isset($eddConfig['item_name'])) {
			return new \WP_Error('missing_config', 'Missing EDD configuration');
		}

		$itemName = is_scalar($eddConfig['item_name']) ? (string)$eddConfig['item_name'] : '';

		if ($itemName === '') {
			return new \WP_Error('empty_config', 'Empty EDD configuration values');
		}

		// Call the custom API.
		$response = wp_remote_post(
			self::STORE_URL,
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
			$this->logApiError('activate', $response);
			return new \WP_Error(
				'notification_license_error',
				'http-error'
			);
		}

		$licenseData = json_decode(wp_remote_retrieve_body($response));

		if ($licenseData->success === false) {
			$this->logApiError('activate', $response);
			return new \WP_Error(
				'notification_license_error',
				$licenseData->error,
				$licenseData
			);
		}

		$licenseData->licenseKey = $licenseKey;
		$this->save($licenseData);

		$driver = new CacheDriver\ObjectCache('notification_license/v2');
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
			self::STORE_URL,
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
			$this->logApiError('deactivate', $response);
			return new \WP_Error(
				'notification_license_error',
				'http-error'
			);
		}

		$licenseData = json_decode(wp_remote_retrieve_body($response));

		// For deactivation, we're more permissive - if the API responds successfully,
		// we allow most statuses since the goal is to remove the license locally
		// Only reject if it's clearly an error response or API failure
		$validDeactivationStatuses = [
			'deactivated',  // Successfully deactivated
			'failed',       // Deactivation failed but that's OK
			'expired',      // License expired
			'inactive',     // Already inactive
			'site_inactive', // Already inactive for this site
			'invalid',      // Invalid license - still want to remove locally
			'revoked',       // Revoked license - still want to remove locally
		];

		if (!in_array($licenseData->license, $validDeactivationStatuses, true)) {
			return new \WP_Error(
				'notification_license_error',
				'deactivation-error',
				$licenseData
			);
		}

		$this->remove();

		$driver = new CacheDriver\ObjectCache('notification_license/v2');
		$cache = new Cache($driver, $this->extension['slug']);
		$cache->delete();

		$driver = new CacheDriver\Transient(ErrorHandler::debugEnabled() ? 60 : DAY_IN_SECONDS);
		$cache = new Cache($driver, sprintf('notification_license_check_%s', $this->extension['slug']));
		$cache->delete();

		return $licenseData;
	}

	/**
	 * Gets the cache key for failed HTTP requests cooldown
	 *
	 * @return string
	 */
	public function getFailedRequestCacheKey(): string
	{
		return 'notification_license_failed_http_' . md5(self::STORE_URL);
	}

	/**
	 * Checks if the license server has recently failed
	 *
	 * @return bool
	 */
	public function hasRecentlyFailed(): bool
	{
		$expiration = get_option($this->getFailedRequestCacheKey());
		return $expiration !== false && (int)$expiration > time();
	}

	/**
	 * Logs a failed HTTP request with 24-hour cooldown
	 *
	 * @return void
	 */
	public function logFailedRequest(): void
	{
		update_option($this->getFailedRequestCacheKey(), strtotime('+24 hours'), false);
	}

	/**
	 * Clears the failed request cooldown
	 *
	 * @return void
	 */
	public function clearFailedRequestCooldown(): void
	{
		delete_option($this->getFailedRequestCacheKey());
	}

	/**
	 * Checks if the stored license data shows a valid status without making an API call
	 *
	 * @return bool
	 */
	public function isStoredValid(): bool
	{
		$licenseData = $this->get();
		return is_object($licenseData)
			&& property_exists($licenseData, 'license')
			&& $licenseData->license === 'valid';
	}

	/**
	 * Gets the raw stored license status string without making an API call
	 *
	 * @return string
	 */
	public function getStoredStatus(): string
	{
		$licenseData = $this->get();
		return is_object($licenseData) && property_exists($licenseData, 'license')
			? (string)$licenseData->license : '';
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
		if ($this->hasRecentlyFailed()) {
			return new \WP_Error('notification_license_error', 'server-cooldown');
		}

		$licenseKey = trim((string)$licenseKey);
		$error = false;

		/** @var string $itemName */
		$itemName = $this->extension['edd']['item_name'];

		// Call the custom API.
		$response = wp_remote_post(
			self::STORE_URL,
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
			$this->logApiError('check', $response);
			$this->logFailedRequest();
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
