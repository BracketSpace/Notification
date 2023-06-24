<?php
/**
 * Email Carrier
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Carrier;

use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;

/**
 * Email Carrier
 */
class Email extends Abstracts\Carrier {

	/**
	 * Carrier icon
	 *
	 * @var string SVG
	 */
	public $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 384"><path d="M448,64H64A64,64,0,0,0,0,128V384a64,64,0,0,0,64,64H448a64,64,0,0,0,64-64V128A64,64,0,0,0,448,64ZM342.66,234.78,478.13,118.69A31.08,31.08,0,0,1,480,128V384c0,2.22-.84,4.19-1.28,6.28ZM448,96c2.13,0,4,.81,6,1.22L256,266.94,58,97.22c2-.41,3.88-1.22,6-1.22ZM33.27,390.25c-.44-2.09-1.27-4-1.27-6.25V128a30.79,30.79,0,0,1,1.89-9.31L169.31,234.75ZM64,416a31,31,0,0,1-9.12-1.84L193.63,255.59l52,44.53a15.92,15.92,0,0,0,20.82,0l52-44.54L457.13,414.16A30.82,30.82,0,0,1,448,416Z" transform="translate(0 -64)"/></svg>';

	/**
	 * Carrier constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		parent::__construct( 'email', __( 'Email', 'notification' ) );
	}

	/**
	 * Used to register Carrier form fields
	 * Uses $this->add_form_field();
	 *
	 * @return void
	 */
	public function form_fields() {

		$this->add_form_field( new Field\InputField( [
			'label' => __( 'Subject', 'notification' ),
			'name'  => 'subject',
		] ) );

		if ( notification_get_setting( 'carriers/email/type' ) === 'html' && ! notification_get_setting( 'carriers/email/unfiltered_html' ) ) {

			$body_field = new Field\EditorField( [
				'label'    => __( 'Body', 'notification' ),
				'name'     => 'body',
				'settings' => [
					'media_buttons' => false,
				],
			] );

		} else {

			$body_field = new Field\CodeEditorField( [
				'label'      => __( 'Body', 'notification' ),
				'name'       => 'body',
				'resolvable' => true,
				'settings'   => [
					'mode'        => 'text/html',
					'lineNumbers' => true,
				],
			] );

		}

		$this->add_form_field( $body_field );

		$this->add_recipients_field();

		if ( notification_get_setting( 'carriers/email/headers' ) ) {

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
	 * Sets mail type to text/html for wp_mail
	 *
	 * @return string mail type
	 */
	public function set_mail_type() {
		return 'text/html';
	}

	/**
	 * Sends the notification
	 *
	 * @param  Triggerable $trigger trigger object.
	 * @return void
	 */
	public function send( Triggerable $trigger ) {
		$default_html_mime = notification_get_setting( 'carriers/email/type' ) === 'html';
		$html_mime         = apply_filters_deprecated( 'notification/email/use_html_mime', [ $default_html_mime, $this, $trigger ], '6.0.0', 'notification/carrier/email/use_html_mime' );
		$html_mime         = apply_filters( 'notification/carrier/email/use_html_mime', $html_mime, $this, $trigger );

		if ( $html_mime ) {
			add_filter( 'wp_mail_content_type', [ $this, 'set_mail_type' ] );
		}

		$data = $this->data;

		$recipients = apply_filters_deprecated( 'notification/email/recipients', [ $data['parsed_recipients'], $this, $trigger ], '6.0.0', 'notification/carrier/email/recipients' );
		$recipients = apply_filters( 'notification/carrier/email/recipients', $recipients, $this, $trigger );

		$subject = apply_filters_deprecated( 'notification/email/subject', [ $data['subject'], $this, $trigger ], '6.0.0', 'notification/carrier/email/subject' );
		$subject = apply_filters( 'notification/carrier/email/subject', $subject, $this, $trigger );

		$message = apply_filters_deprecated( 'notification/email/message/pre', [ $data['body'], $this, $trigger ], '6.0.0', 'notification/carrier/email/message/pre' );
		$message = apply_filters( 'notification/carrier/email/message/pre', $message, $this, $trigger );

		$use_autop = apply_filters_deprecated( 'notification/email/message/use_autop', [ $html_mime, $this, $trigger ], '6.0.0', 'notification/carrier/email/message/use_autop' );
		$use_autop = apply_filters( 'notification/carrier/email/message/use_autop', $use_autop, $this, $trigger );
		if ( $use_autop ) {
			$message = wpautop( $message );
		}

		$message = apply_filters_deprecated( 'notification/email/message', [ $message, $this, $trigger ], '6.0.0', 'notification/carrier/email/message' );
		$message = apply_filters( 'notification/carrier/email/message', $message, $this, $trigger );

		// Fix for wp_mail not being processed with empty message.
		if ( empty( $message ) ) {
			$message = ' ';
		}

		$headers = [];
		if ( notification_get_setting( 'carriers/email/headers' ) && ! empty( $data['headers'] ) ) {
			foreach ( $data['headers'] as $header ) {
				$headers[] = $header['key'] . ': ' . $header['value'];
			}
		}

		$headers = apply_filters_deprecated( 'notification/email/headers', [ $headers, $this, $trigger ], '6.0.0', 'notification/carrier/email/headers' );
		$headers = apply_filters( 'notification/carrier/email/headers', $headers, $this, $trigger );

		$attachments = apply_filters_deprecated( 'notification/email/attachments', [ [], $this, $trigger ], '6.0.0', 'notification/carrier/email/attachments' );
		$attachments = apply_filters( 'notification/carrier/email/attachments', $attachments, $this, $trigger );

		$errors = [];

		// Fire an email one by one.
		foreach ( $recipients as $to ) {
			try {
				wp_mail( $to, $subject, $message, $headers, $attachments );
			} catch ( \Exception $e ) {
				if ( ! isset( $errors[ $e->getMessage() ] ) ) {
					$errors[ $e->getMessage() ] = [
						'recipients' => [],
					];
				}

				$errors[ $e->getMessage() ]['recipients'][] = $to;
			}
		}

		foreach ( $errors as $error => $error_data ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			notification_log( $this->get_name(), 'error', '<pre>' . print_r( [
				'error'               => $error,
				'recipients_affected' => $error_data['recipients'],
				'trigger'             => sprintf( '%s (%s)', $trigger->get_name(), $trigger->get_slug() ),
				'email_subject'       => $subject,
			], true ) . '</pre>' );
		}

		if ( $html_mime ) {
			remove_filter( 'wp_mail_content_type', [ $this, 'set_mail_type' ] );
		}
	}

	/**
	 * Replaces the filtered body with the unfiltered one if the notifications/email/unfiltered_html setting is set to true.
	 *
	 * @filter notification/carrier/form/data/values
	 *
	 * @param  array $carrier_data      Carrier data from PostData.
	 * @param  array $raw_data          Raw data from PostData, it contains the unfiltered message body.
	 * @return array                    Carrier data with the unfiltered body, if notifications/email/unfiltered_html setting is true.
	 **/
	public function allow_unfiltered_html_body( $carrier_data, $raw_data ) {
		if ( notification_get_setting( 'carriers/email/unfiltered_html' ) ) {
			$carrier_data['body'] = $raw_data['body'];
		}

		return $carrier_data;
	}

}
