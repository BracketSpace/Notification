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
	 * Method sends request to API, based on response checks whether REST API works correctly
	 *
	 * @since [Next]
	 * @action admin_notices
	 * @return void
	 */
	public function test_rest_api() {
		$response = wp_remote_get( get_rest_url( null, 'notification/v1/check/' ) );
		$message  = json_decode( wp_remote_retrieve_body( $response ), true );

		$is_available = false;
		$is_edit      = false;

		if ( array_key_exists( 'data', $message ) ) {
			$is_available = 'RestApi' === $message['data'];
		}

		$current_screen = get_current_screen();

		if ( $current_screen instanceof \WP_Screen ) {
			$is_edit = 'post' === $current_screen->base && 'notification' === $current_screen->post_type;
		}

		if ( ! $is_available && $is_edit ) {
			$class = 'notice notice-error';
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), 'The Notification plugin requires enabled REST API endpoint: notification/v1/. Please ensure your WP REST API works correctly.' );
		}
	}

}
