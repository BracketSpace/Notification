<?php
/**
 * License class
 * Used by paid extensions to save and retrieve license from database
 * License is used to provide the updates
 *
 * @package notification
 */

namespace BracketSpace\Notification\Core;

use BracketSpace\Notification\ErrorHandler;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;

/**
 * License class
 */
class License {

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
	protected $license_storage = 'notification_licenses';

	/**
	 * Class constructor
	 *
	 * @since 5.1.0
	 * @param array $extension extension data.
	 */
	public function __construct( array $extension ) {
		$this->extension = $extension;
	}

	/**
	 * Gets all licenses from database
	 *
	 * @since  5.1.0
	 * @return array licenses
	 */
	public function get_licenses() {
		return get_option( $this->license_storage, [] );
	}

	/**
	 * Gets single license info
	 *
	 * @since  5.1.0
	 * @return mixed license data or false
	 */
	public function get() {
		$driver = new CacheDriver\ObjectCache( 'notification_license', ErrorHandler::debug_enabled() ? 0 : 1 );
		$cache  = new Cache( $driver, $this->extension['slug'] );

		return $cache->collect( function () {
			$licenses = $this->get_licenses();
			$license  = false;

			if ( isset( $licenses[ $this->extension['slug'] ] ) ) {
				$license = $licenses[ $this->extension['slug'] ];
			}

			return $license;
		} );
	}

	/**
	 * Checks if license is valid
	 *
	 * @since  5.1.0
	 * @return boolean
	 */
	public function is_valid() {
		$license_data = $this->get();

		if ( empty( $license_data ) ) {
			return false;
		}

		$driver = new CacheDriver\Transient( ErrorHandler::debug_enabled() ? DAY_IN_SECONDS : 1 );
		$cache  = new Cache( $driver, sprintf( 'notification_license_check_%s', $this->extension['slug'] ) );

		return $cache->collect( function () use ( $license_data ) {
			$license_check = $this->check( $license_data->license_key );

			if ( is_wp_error( $license_check ) ) {
				return 'valid' === $license_data->license;
			}

			$license_check->license_key = $license_data->license_key;
			$license_data               = $license_check;
			$this->save( $license_data );

			return 'valid' === $license_data->license;
		} );
	}

	/**
	 * Gets the license key
	 *
	 * @since  7.1.1
	 * @return string
	 */
	public function get_key() {
		$license_data = $this->get();
		return $license_data->license_key;
	}

	/**
	 * Saves single license info
	 *
	 * @since  5.1.0
	 * @param object $license_data license data from API.
	 * @return void
	 */
	public function save( $license_data ) {
		$driver = new CacheDriver\ObjectCache( 'notification_license' );
		$cache  = new Cache( $driver, $this->extension['slug'] );
		$cache->set( $license_data );

		$licenses                             = $this->get_licenses();
		$licenses[ $this->extension['slug'] ] = $license_data;

		update_option( $this->license_storage, $licenses );
	}

	/**
	 * Removes single license from database
	 *
	 * @since  5.1.0
	 * @return void
	 */
	public function remove() {
		$driver = new CacheDriver\ObjectCache( 'notification_license' );
		$cache  = new Cache( $driver, $this->extension['slug'] );
		$cache->delete();

		$licenses = $this->get_licenses();
		if ( isset( $licenses[ $this->extension['slug'] ] ) ) {
			unset( $licenses[ $this->extension['slug'] ] );
		}

		update_option( $this->license_storage, $licenses );
	}

	/**
	 * Activates the license
	 *
	 * @since  5.1.0
	 * @param  string $license_key license key.
	 * @return mixed               WP_Error or License data
	 */
	public function activate( $license_key = '' ) {

		$license_key = trim( $license_key );
		$error       = false;

		// Call the custom API.
		$response = wp_remote_post(
			$this->extension['edd']['store_url'],
			[
				'timeout' => 15,
				'body'    => [
					'edd_action' => 'activate_license',
					'license'    => $license_key,
					'item_name'  => rawurlencode( $this->extension['edd']['item_name'] ),
					'url'        => home_url(),
				],
			]
		);

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new \WP_Error( 'notification_license_error', 'http-error' );
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( false === $license_data->success ) {
			return new \WP_Error( 'notification_license_error', $license_data->error, $license_data );
		}

		$license_data->license_key = $license_key;
		$this->save( $license_data );

		return $license_data;

	}

	/**
	 * Deactivates the license
	 *
	 * @since  5.1.0
	 * @return mixed WP_Error or License data
	 */
	public function deactivate() {

		$license_data = $this->get();
		$error        = false;

		// Call the custom API.
		$response = wp_remote_post(
			$this->extension['edd']['store_url'],
			[
				'timeout' => 15,
				'body'    => [
					'edd_action' => 'deactivate_license',
					'license'    => trim( $license_data->license_key ),
					'item_name'  => rawurlencode( $this->extension['edd']['item_name'] ),
					'url'        => home_url(),
				],
			]
		);

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new \WP_Error( 'notification_license_error', 'http-error' );
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! in_array( $license_data->license, [ 'deactivated', 'failed' ], true ) ) {
			return new \WP_Error( 'notification_license_error', 'deactivation-error' );
		}

		$this->remove();

		return $license_data;

	}

	/**
	 * Checks the license
	 *
	 * @since  5.1.0
	 * @param  string $license_key license key.
	 * @return object              WP_Error or license object
	 */
	public function check( $license_key = '' ) {

		$license_key = trim( $license_key );
		$error       = false;

		// Call the custom API.
		$response = wp_remote_post(
			$this->extension['edd']['store_url'],
			[
				'timeout' => 15,
				'body'    => [
					'edd_action' => 'check_license',
					'license'    => $license_key,
					'item_name'  => rawurlencode( $this->extension['edd']['item_name'] ),
					'url'        => home_url(),
				],
			]
		);

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new \WP_Error( 'notification_license_error', 'http-error' );
		}

		return json_decode( wp_remote_retrieve_body( $response ) );

	}

}
