<?php

/**
 * Trait for users database operations.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Traits;

trait Webhook
{

	/**
	 * Carrier constructor
	 *
	 * @param string $name Webhook nice name.
	 * @return void
	 * @since  7.0.0
	 */
	public function __construct($name)
	{
		$slug = strtolower(
			str_replace(
				' ',
				'_',
				$name
			)
		);

		parent::__construct(
			$slug,
			__(
				$name,
				'notification'
			)
		);
	}

	/**
	 * Makes http request
	 *
	 * @param string $url URL to call.
	 * @param array $args Arguments. Default: empty.
	 * @param array $headers Headers. Default: empty.
	 * @param string $method HTTP request method.
	 * @return void
	 * @since  7.0.0
	 */
	public function httpRequest($url, $args = [], $headers = [], $method = 'GET')
	{
		$remoteArgs = apply_filters(
			"notification/carrier/webhook/remote_args/{$method}",
			[
				'body' => $args,
				'headers' => $headers,
				'method' => strtoupper($method),
			],
			$url,
			$args,
			$this
		);

		$response = wp_remote_request(
			$url,
			$remoteArgs
		);

		if (is_wp_error($response)) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			notification_log(
				$this->getName(),
				'error',
				'<pre>' . print_r(
					[
						'url' => $url,
						'args' => $remoteArgs,
						'errors' => $response->getErrorMessages(),
					],
					true
				) . '</pre>'
			);
		}

		$code = wp_remote_retrieve_response_code($response);

		if (200 > $code || 300 <= $code) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			notification_log(
				$this->getName(),
				'warning',
				'<pre>' . print_r(
					[
						'url' => $url,
						'args' => $remoteArgs,
						'response_code' => $code,
						'message' => wp_remote_retrieve_response_message($response),
					],
					true
				) . '</pre>'
			);
		}

		do_action(
			"notification/carrier/webhook/called/{$method}",
			$response,
			$url,
			$args,
			$remoteArgs,
			$this
		);
	}

	/**
	 * Parses args to be understand by the wp_remote_* functions
	 *
	 * @param array $args Args from saved fields.
	 * @return array       Parsed args as key => value array
	 * @since  7.0.0
	 */
	private function parseArgs($args)
	{
		$parsedArgs = [];

		if (empty($args)) {
			return $parsedArgs;
		}

		foreach ($args as $arg) {
			if (isset($arg['hide']) && $arg['hide']) {
				continue;
			}

			$parsedArgs[$arg['key']] = $arg['value'];
		}

		return $parsedArgs;
	}
}
