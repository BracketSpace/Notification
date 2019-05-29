<?php
/**
 * Webhook Carrier
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Carrier;

use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;

/**
 * Webhook Carrier
 */
class Webhook extends Abstracts\Carrier {

	/**
	 * Carrier constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		parent::__construct( 'webhook', __( 'Webhook', 'notification' ) );
	}

	/**
	 * Used to register Carrier form fields
	 * Uses $this->add_form_field();
	 *
	 * @return void
	 */
	public function form_fields() {

		$this->add_form_field( new Field\RecipientsField( [
			'carrier'          => 'webhook',
			'label'            => __( 'URLs', 'notification' ),
			'name'             => 'urls',
			'add_button_label' => __( 'Add URL', 'notification' ),
		] ) );

		$this->add_form_field( new Field\RepeaterField( [
			'label'            => __( 'Arguments', 'notification' ),
			'name'             => 'args',
			'add_button_label' => __( 'Add argument', 'notification' ),
			'fields'           => [
				new Field\InputField( [
					'label'       => __( 'Key', 'notification' ),
					'name'        => 'key',
					'resolvable'  => true,
					'description' => __( 'You can use merge tags', 'notification' ),
				] ),
				new Field\InputField( [
					'label'       => __( 'Value', 'notification' ),
					'name'        => 'value',
					'resolvable'  => true,
					'description' => __( 'You can use merge tags', 'notification' ),
				] ),
			],
		] ) );

		$this->add_form_field( new Field\CheckboxField( [
			'label'          => __( 'JSON', 'notification' ),
			'name'           => 'json',
			'checkbox_label' => __( 'Send the arguments in JSON format', 'notification' ),
		] ) );

		if ( notification_get_setting( 'notifications/webhook/headers' ) ) {

			$this->add_form_field( new Field\RepeaterField( [
				'label'            => __( 'Headers', 'notification' ),
				'name'             => 'headers',
				'add_button_label' => __( 'Add header', 'notification' ),
				'fields'           => [
					new Field\InputField( [
						'label'       => __( 'Key', 'notification' ),
						'name'        => 'key',
						'resolvable'  => true,
						'description' => __( 'You can use merge tags', 'notification' ),
					] ),
					new Field\InputField( [
						'label'       => __( 'Value', 'notification' ),
						'name'        => 'value',
						'resolvable'  => true,
						'description' => __( 'You can use merge tags', 'notification' ),
					] ),
				],
			] ) );

		}

	}

	/**
	 * Sends the notification
	 *
	 * @param  Triggerable $trigger trigger object.
	 * @return void
	 */
	public function send( Triggerable $trigger ) {

		$data = $this->data;

		$args = $this->parse_args( $data['args'] );
		$args = apply_filters_deprecated( 'notification/webhook/args', [ $args, $this, $trigger ], '6.0.0', 'notification/carrier/webhook/args' );
		$args = apply_filters( 'notification/carrier/webhook/args', $args, $this, $trigger );

		if ( $data['json'] ) {
			$args = wp_json_encode( $args );
		}

		// Headers.
		if ( $data['json'] ) {
			$headers = [ 'Content-Type' => 'application/json' ];
		} else {
			$headers = [];
		}

		if ( notification_get_setting( 'notifications/webhook/headers' ) ) {
			$headers = array_merge( $headers, $this->parse_args( $data['headers'] ) );
		}

		// Call each URL separately.
		foreach ( $data['urls'] as $url ) {
			$filtered_args = apply_filters_deprecated( 'notification/webhook/args/' . $url['type'], [ $args, $this, $trigger ], '6.0.0', 'notification/carrier/webhook/args/' . $url['type'] );
			$filtered_args = apply_filters( 'notification/carrier/webhook/args/' . $url['type'], $filtered_args, $this, $trigger );

			if ( 'get' === $url['type'] ) {
				$this->send_get( $url['recipient'], $filtered_args, $headers );
			} elseif ( 'post' === $url['type'] ) {
				$this->send_post( $url['recipient'], $filtered_args, $headers );
			}
		}

	}

	/**
	 * Sends GET request
	 *
	 * @since  5.0.0
	 * @param  string $url  URL to call.
	 * @param  array  $args    arguments.
	 * @param  array  $headers headers.
	 * @return void
	 */
	public function send_get( $url, $args = [], $headers = [] ) {

		$remote_url = add_query_arg( $args, $url );

		$remote_args = apply_filters_deprecated( 'notification/webhook/remote_args/get', [
			[
				'headers' => $headers,
			],
			$url,
			$args,
			$this,
		], '6.0.0', 'notification/carrier/webhook/remote_args/get' );

		$remote_args = apply_filters( 'notification/carrier/webhook/remote_args/get', $remote_args, $url, $args, $this );

		$response = wp_remote_get( $remote_url, $remote_args );

		do_action_deprecated( 'notification/webhook/called/get', [
			$response,
			$url,
			$args,
			$remote_args,
			$this,
		], '6.0.0', 'notification/carrier/webhook/called/get' );
		do_action( 'notification/carrier/webhook/called/get', $response, $url, $args, $remote_args, $this );

	}

	/**
	 * Sends POST request
	 *
	 * @since  5.0.0
	 * @param  string $url  URL to call.
	 * @param  array  $args    arguments.
	 * @param  array  $headers headers.
	 * @return void
	 */
	public function send_post( $url, $args = [], $headers = [] ) {

		$remote_args = apply_filters_deprecated( 'notification/webhook/remote_args/post', [
			[
				'body'    => $args,
				'headers' => $headers,
			],
			$url,
			$args,
			$this,
		], '6.0.0', 'notification/carrier/webhook/remote_args/post' );

		$remote_args = apply_filters( 'notification/carrier/webhook/remote_args/post', $remote_args, $url, $args, $this );

		$response = wp_remote_post( $url, $remote_args );

		do_action_deprecated( 'notification/webhook/called/post', [
			$response,
			$url,
			$args,
			$remote_args,
			$this,
		], '6.0.0', 'notification/carrier/webhook/called/post' );
		do_action( 'notification/carrier/webhook/called/post', $response, $url, $args, $remote_args, $this );

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
			$parsed_args[ $arg['key'] ] = $arg['value'];
		}

		return $parsed_args;

	}

}
