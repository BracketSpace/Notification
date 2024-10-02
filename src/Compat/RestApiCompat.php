<?php
/**
 * RestApi compat class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Compat;

use BracketSpace\Notification\Api\Api;

/**
 * RestApi compat class
 */
class RestApiCompat
{
	/**
	 * Method sends request to API, based on response checks whether REST API works correctly
	 *
	 * @action admin_notices
	 *
	 * @return void
	 * @since 8.0.12
	 */
	public function testRestApi()
	{
		$isEdit = false;
		$currentScreen = get_current_screen();

		if ($currentScreen instanceof \WP_Screen) {
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			$isEdit = $currentScreen->base === 'post' && $currentScreen->post_type === 'notification';
		}

		if (! $isEdit) {
			return;
		}

		$response = wp_remote_get(get_rest_url(null, \Notification::component(Api::class)->getEndpoint('check')));

		$message = json_decode(wp_remote_retrieve_body($response), true);

		$isAvailable = false;

		if (
			is_array($message) &&
			array_key_exists('data', $message) &&
			$message['data'] === 'RestApi'
		) {
			return;
		}

		printf(
			'<div class="notice notice-error"><p>%1$s</p></div>',
			esc_html__(
				"The Notification plugin requires enabled REST API endpoint: notification/v1/.
				Please ensure your WP REST API works correctly and you're not blocking this endpoint from access.",
				'notification'
			)
		);
	}
}
