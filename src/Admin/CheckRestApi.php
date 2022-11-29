<?php

/**
 * CheckRestApi class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

/**
 * CheckRestApi class
 */
class CheckRestApi
{

	/**
	 * Method sends request to API, based on response checks whether REST API works correctly
	 *
	 * @return void
	 * @since 8.0.12
	 * @action admin_notices
	 */
	public function testRestApi()
	{
		$isEdit = false;
		$currentScreen = get_current_screen();

		if ($currentScreen instanceof \WP_Screen) {
			$isEdit = $currentScreen->base === 'post' && $currentScreen->postType === 'notification';
		}

		if (!$isEdit) {
			return;
		}

		$response = wp_remote_get(
			get_rest_url(
				null,
				\Notification::component('api')->getEndpoint('check')
			)
		);

		$message = json_decode(
			wp_remote_retrieve_body($response),
			true
		);

		$isAvailable = false;

		if (
			is_array($message) && array_key_exists(
				'data',
				$message
			) && $message['data'] === 'RestApi'
		) {
			return;
		}

		printf(
			'<div class="notice notice-error"><p>%1$s</p></div>',
			'The Notification plugin requires enabled REST API endpoint: notification/v1/. Please ensure your WP REST API works correctly.'
		);
	}
}
