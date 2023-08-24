<?php

/**
 * Trait for users database operations.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Traits;

use function BracketSpace\Notification\log;

/**
 * Webhook trait
 */
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
		$slug = strtolower(str_replace(' ', '_', $name));

		parent::__construct($slug, __($name, 'notification'));
	}

	/**
	 * Makes http request
	 *
	 * @param string $url URL to call.
	 * @param array<mixed> $args Arguments. Default: empty.
	 * @param array<mixed> $headers Headers. Default: empty.
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

		$response = wp_remote_request($url, $remoteArgs);

		if (is_wp_error($response)) {
			log(
				$this->getName(),
				'error',
				// phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
				'<pre>' . print_r(
					[
						'url' => $url,
						'args' => $remoteArgs,
						'errors' => $response->get_error_messages(),
					],
					true
				) . '</pre>'
			);
		}

		$code = wp_remote_retrieve_response_code($response);

		if ($code < 200 || $code >= 300) {
			log(
				$this->getName(),
				'warning',
				// phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
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
	 * @param array<mixed> $args Args from saved fields.
	 * @return array<mixed>       Parsed args as key => value array
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
