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
	 * @param  string $name Webhook nice name.
	 * @since  7.0.0
	 * @return void
	 */
	public function __construct( $name ) {
		$slug = strtolower( str_replace( ' ', '_', $name ) );

		parent::__construct( $slug, __( $name, 'notification' ) );
	}

	/**
	 * Makes http request
	 *
	 * @since  7.0.0
	 * @param  string $url     URL to call.
	 * @param  array  $args    Arguments. Default: empty.
	 * @param  array  $headers Headers. Default: empty.
	 * @param  string $method  HTTP request method.
	 * @return void
	 */
	public function http_request( $url, $args = [], $headers = [], $method ) {
		$remote_args = apply_filters(
			"notification/carrier/webhook/remote_args/{$method}",
			[
				'body'    => $args,
				'headers' => $headers,
				'method'  => strtoupper( $method ),
			],
			$url,
			$args,
			$this
		);

		$response = wp_remote_request( $url, $remote_args );

		if ( is_wp_error( $response ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			notification_log( $this->get_name(), 'error', '<pre>' . print_r( [
				'url'    => $url,
				'args'   => $remote_args,
				'errors' => $response->get_error_messages(),
			], true ) . '</pre>' );
		}

		do_action( "notification/carrier/webhook/called/{$method}", $response, $url, $args, $remote_args, $this );
	}

	/**
	 * Parses args to be understand by the wp_remote_* functions
	 *
	 * @since  7.0.0
	 * @param  array $args Args from saved fields.
	 * @return array       Parsed args as key => value array
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
