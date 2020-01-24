<?php
/**
 * Trait for users database operations.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

trait Webhook {

	/**
	 * Carrier constructor
	 *
	 * @param string $slug Webhook slug.
	 * @since 5.0.0
	 */
	public function __construct( $slug ) {
		parent::__construct( $slug, __( ucfirst( $slug ), 'notification' ) );
	}

	/**
	 * Makes http request
	 *
	 * @since  [Next]
	 * @param  string $url  URL to call.
	 * @param  array  $args    arguments.
	 * @param  array  $headers headers.
	 * @param string $method Http request method.
	 * @return void
	 */
	public function http_request( $url, $args = [], $headers = [], $method ) {

		$remote_args = apply_filters_deprecated( "notification/webhook/remote_args/{$method}", [
			[
				'body'    => $args,
				'headers' => $headers,
				'method'  => strtoupper( $method ),
			],
			$url,
			$args,
			$this,
		], '6.0.0', "notification/carrier/webhook/remote_args/{$method}" );

		$remote_args = apply_filters( "notification/carrier/webhook/remote_args/{$method}", $remote_args, $url, $args, $this );

		$response = wp_remote_request( $url, $remote_args );

		do_action_deprecated( "notification/webhook/called/{$method}", [
			$response,
			$url,
			$args,
			$remote_args,
			$this,
		], '6.0.0', "notification/carrier/webhook/called/{$method}" );

		do_action( "notification/carrier/webhook/called/{$method}", $response, $url, $args, $remote_args, $this );

	}

	/**
	 * Parses args to be understand by the wp_remote_* functions
	 *
	 * @since  5.0.0
	 * @param  array $args args from saved fields.
	 * @return array       parsed args as key => value array
	 */
	private function parse_args( $args ) {

		$parsed_args = [];

		if ( empty( $args ) ) {
			return $parsed_args;
		}

		foreach ( $args as $arg ) {
			if ( isset( $arg['hide'] ) && $arg['hide'] ) {
				continue;
			}

			$parsed_args[ $arg['key'] ] = $arg['value'];
		}

		return $parsed_args;

	}
}
