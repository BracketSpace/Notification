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

		if ( notification_get_setting( 'notifications/email/type' ) === 'html' && ! notification_get_setting( 'notifications/email/unfiltered_html' ) ) {

			$body_field = new Field\EditorField( [
				'label'    => __( 'Body', 'notification' ),
				'name'     => 'body',
				'settings' => [
					'media_buttons' => false,
				],
			] );

		} else {

			$body_field = new Field\TextareaField( [
				'label' => __( 'Body', 'notification' ),
				'name'  => 'body',
			] );

		}

		$this->add_form_field( $body_field );

		$this->add_form_field( new Field\RecipientsField( [
			'carrier' => $this->get_slug(),
		] ) );

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

		$default_html_mime = notification_get_setting( 'notifications/email/type' ) === 'html';
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

		$headers = apply_filters_deprecated( 'notification/email/headers', [ [], $this, $trigger ], '6.0.0', 'notification/carrier/email/headers' );
		$headers = apply_filters( 'notification/carrier/email/headers', $headers, $this, $trigger );

		$attachments = apply_filters_deprecated( 'notification/email/attachments', [ [], $this, $trigger ], '6.0.0', 'notification/carrier/email/attachments' );
		$attachments = apply_filters( 'notification/carrier/email/attachments', $attachments, $this, $trigger );

		// Fire an email one by one.
		foreach ( $recipients as $to ) {
			wp_mail( $to, $subject, $message, $headers, $attachments );
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

		if ( notification_get_setting( 'notifications/email/unfiltered_html' ) ) {
			$carrier_data['body'] = $raw_data['body'];
		}

		return $carrier_data;

	}

}
