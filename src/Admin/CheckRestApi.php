<?php
/**
 * CheckRestApi class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Dependencies\Micropackage\DocHooks\HookTrait;

/**
 * CheckRestApi class
 */
class CheckRestApi {

	/**
	 * @since [Next]
	 * @action admin_notices
	 * @return void
	 */
	public function test_rest_api() {
		$response = wp_remote_get( get_rest_url( null, 'notification/v1/check/' ) );
		$message  = json_decode( wp_remote_retrieve_body( $response ), true );

		$is_available = false;

		if ( array_key_exists( 'data', $message ) ) {
			$is_available = 'RestApi' === $message['data'];
		}

		if ( ! $is_available ) {
			$class = 'notice notice-error';
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), 'For correct Notification plugin operations it is required to enable REST API' );
		}
	}

}
