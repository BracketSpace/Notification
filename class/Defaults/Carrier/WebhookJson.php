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
use BracketSpace\Notification\Traits\Webhook as WebhookTrait;
/**
 * Webhook Carrier
 */
class WebhookJson extends Abstracts\Carrier {
	use WebhookTrait;

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

		$this->add_form_field( new Field\EditorField( [
			'label'       => __( 'JSON', 'notification' ),
			'name'        => 'json',
			'resolvable'  => true,
			'description' => __( 'You can use merge tags', 'notification' ),
		] ) );

		if ( notification_get_setting( 'notifications/webhook/headers' ) ) {

			$this->add_form_field( new Field\RepeaterField( [
				'label'            => __( 'Headers', 'notification' ),
				'name'             => 'headers',
				'add_button_label' => __( 'Add header', 'notification' ),
				'fields'           => [
					new Field\CheckboxField(
						[
							'label'          => __( 'Hide', 'notification-slack' ),
							'name'           => 'hide',
							'checkbox_label' => __( 'Hide if empty value', 'notification' ),
						]
					),
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
			$args = $data['json'];
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

			$this->http_request( $url['recipient'], $filtered_args, $headers, $url['type'] );
		}

	}

}
