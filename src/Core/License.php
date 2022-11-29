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
	 * @var array
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
	 * @since 5.1.0
	 * @param array $extension extension data.
	 */
	public function __construct( array $extension )
	{
		$this->extension = $extension;
	}

	/**
	 * Gets all licenses from database
	 *
	 * @since  5.1.0
	 * @return array licenses
	 */
	public function get_licenses()
	{
		return get_option($this->license_storage, []);
	}

	/**
	 * Gets single license info
	 *
	 * @since  5.1.0
	 * @return mixed license data or false
	 */
	public function get()
	{
		$driver = new CacheDriver\ObjectCache('notification_license');
		$cache = new Cache($driver, $this->extension['slug']);

		return $cache->collect(
			function () {
				$licenses = $this->get_licenses();
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
	 * @since  5.1.0
	 * @return bool
	 */
	public function is_valid()
	{
		$licenseData = $this->get();

		if (empty($licenseData)) {
			return false;
		}

		$driver = new CacheDriver\Transient(ErrorHandler::debug_enabled() ? 60 : DAY_IN_SECONDS);
		$cache = new Cache($driver, sprintf('notification_license_check_%s', $this->extension['slug']));

		return $cache->collect(
			function () use ( $licenseData ) {
				$licenseCheck = $this->check($licenseData->license_key);

				if (is_wp_error($licenseCheck)) {
					return $licenseData->license === 'valid';
				}

				$licenseCheck->license_key = $licenseData->license_key;
				$licenseData = $licenseCheck;
				$this->save($licenseData);

				return $licenseData->license === 'valid';
			}
		);
	}

	/**
	 * Gets the license key
	 *
	 * @since  7.1.1
	 * @return string
	 */
	public function get_key()
	{
		$licenseData = $this->get();
		return $licenseData->license_key ?? '';
	}

	/**
	 * Saves single license info
	 *
	 * @since  5.1.0
	 * @param object $licenseData license data from API.
	 * @return void
	 */
	public function save( $licenseData )
	{
		$driver = new CacheDriver\ObjectCache('notification_license');
		$cache = new Cache($driver, $this->extension['slug']);
		$cache->set($licenseData);

		$licenses = $this->get_licenses();
		$licenses[$this->extension['slug']] = $licenseData;

		update_option($this->license_storage, $licenses);
	}

	/**
	 * Removes single license from database
	 *
	 * @since  5.1.0
	 * @return void
	 */
	public function remove()
	{
		$driver = new CacheDriver\ObjectCache('notification_license');
		$cache = new Cache($driver, $this->extension['slug']);
		$cache->delete();

		$licenses = $this->get_licenses();
		if (isset($licenses[$this->extension['slug']])) {
			unset($licenses[$this->extension['slug']]);
		}

		update_option($this->license_storage, $licenses);
	}

	/**
	 * Activates the license
	 *
	 * @since  5.1.0
	 * @param  string $licenseKey license key.
	 * @return mixed               WP_Error or License data
	 */
	public function activate( $licenseKey = '' )
	{

		$licenseKey = trim($licenseKey);
		$error = false;

		// Call the custom API.
		$response = wp_remote_post(
			$this->extension['edd']['store_url'],
			[
				'timeout' => 15,
				'body' => [
					'edd_action' => 'activate_license',
					'license' => $licenseKey,
					'item_name' => rawurlencode($this->extension['edd']['item_name']),
					'url' => home_url(),
				],
			]
		);

		// Make sure the response came back okay.
		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
			return new \WP_Error('notification_license_error', 'http-error');
		}

		$licenseData = json_decode(wp_remote_retrieve_body($response));

		if ($licenseData->success === false) {
			return new \WP_Error('notification_license_error', $licenseData->error, $licenseData);
		}

		$licenseData->license_key = $licenseKey;
		$this->save($licenseData);

		return $licenseData;
	}

	/**
	 * Deactivates the license
	 *
	 * @since  5.1.0
	 * @return mixed WP_Error or License data
	 */
	public function deactivate()
	{

		$licenseData = $this->get();
		$error = false;

		// Call the custom API.
		$response = wp_remote_post(
			$this->extension['edd']['store_url'],
			[
				'timeout' => 15,
				'body' => [
					'edd_action' => 'deactivate_license',
					'license' => trim($licenseData->license_key),
					'item_name' => rawurlencode($this->extension['edd']['item_name']),
					'url' => home_url(),
				],
			]
		);

		// Make sure the response came back okay.
		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
			return new \WP_Error('notification_license_error', 'http-error');
		}

		$licenseData = json_decode(wp_remote_retrieve_body($response));

		if (! in_array($licenseData->license, [ 'deactivated', 'failed' ], true)) {
			return new \WP_Error('notification_license_error', 'deactivation-error');
		}

		$this->remove();

		return $licenseData;
	}

	/**
	 * Checks the license
	 *
	 * @since  5.1.0
	 * @param  string $licenseKey license key.
	 * @return object              WP_Error or license object
	 */
	public function check( $licenseKey = '' )
	{

		$licenseKey = trim($licenseKey);
		$error = false;

		// Call the custom API.
		$response = wp_remote_post(
			$this->extension['edd']['store_url'],
			[
				'timeout' => 15,
				'body' => [
					'edd_action' => 'check_license',
					'license' => $licenseKey,
					'item_name' => rawurlencode($this->extension['edd']['item_name']),
					'url' => home_url(),
				],
			]
		);

		// Make sure the response came back okay.
		if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
			return new \WP_Error('notification_license_error', 'http-error');
		}

		return json_decode(wp_remote_retrieve_body($response));
	}
}
