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
	 * @since 8.0.12
	 * @action admin_notices
	 * @return void
	 */
	public function test_rest_api() {
		$is_edit        = false;
		$current_screen = get_current_screen();

		if ( $current_screen instanceof \WP_Screen ) {
			$is_edit = 'post' === $current_screen->base && 'notification' === $current_screen->post_type;
		}

		if ( ! $is_edit ) {
			return;
		}

		$response = wp_remote_get( get_rest_url(
			null,
			\Notification::component( 'api' )->get_endpoint( 'check' )
		) );

		$message = json_decode( wp_remote_retrieve_body( $response ), true );

		$is_available = false;

		if ( ! is_array( $message ) || ! array_key_exists( 'data', $message ) || 'RestApi' !== $message['data'] ) {
			printf(
				'<div class="notice notice-error"><p>%1$s</p></div>',
				'The Notification plugin requires enabled REST API endpoint: notification/v1/. Please ensure your WP REST API works correctly.'
			);
		}
	}

}
