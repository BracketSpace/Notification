<?php
/**
 * Email notification
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Notification;

use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;

/**
 * Email notification
 */
class Email extends Abstracts\Notification {

	/**
	 * Notification constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		parent::__construct( 'email', __( 'Email', 'notification' ) );
	}

	/**
	 * Used to register notification form fields
	 * Uses $this->add_form_field();
	 *
	 * @return void
	 */
	public function form_fields() {

		$this->add_form_field(
			new Field\InputField(
				array(
					'label' => __( 'Subject', 'notification' ),
					'name'  => 'subject',
				)
			)
		);

		if ( notification_get_setting( 'notifications/email/type' ) === 'html' && ! notification_get_setting( 'notifications/email/unfiltered_html' ) ) {
			$body_field = new Field\EditorField(
				array(
					'label'    => __( 'Body', 'notification' ),
					'name'     => 'body',
					'settings' => array(
						'media_buttons' => false,
					),
				)
			);
		} else {
			$body_field = new Field\TextareaField(
				array(
					'label' => __( 'Body', 'notification' ),
					'name'  => 'body',
				)
			);
		}

		$this->add_form_field( $body_field );

		$this->add_form_field(
			new Field\RecipientsField(
				array(
					'notification' => $this->get_slug(),
				)
			)
		);

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
		$html_mime         = apply_filters( 'notification/' . $this->get_slug() . '/use_html_mime', $default_html_mime, $this, $trigger );

		if ( $html_mime ) {
			add_filter( 'wp_mail_content_type', array( $this, 'set_mail_type' ) );
		}

		$data = $this->data;

		$recipients = apply_filters( 'notification/' . $this->get_slug() . '/recipients', $data['parsed_recipients'], $this, $trigger );

		$subject = apply_filters( 'notification/' . $this->get_slug() . '/subject', $data['subject'], $this, $trigger );

		$message = apply_filters( 'notification/' . $this->get_slug() . '/message/pre', $data['body'], $this, $trigger );
		if ( apply_filters( 'notification/' . $this->get_slug() . '/message/use_autop', $html_mime, $this, $trigger ) ) {
			$message = wpautop( $message );
		}
		$message = apply_filters( 'notification/' . $this->get_slug() . '/message', $message, $this, $trigger );

		// Fix for wp_mail not being processed with empty message.
		if ( empty( $message ) ) {
			$message = ' ';
		}

		$headers     = apply_filters( 'notification/' . $this->get_slug() . '/headers', array(), $this, $trigger );
		$attachments = apply_filters( 'notification/' . $this->get_slug() . '/attachments', array(), $this, $trigger );

		// Fire an email one by one.
		foreach ( $recipients as $to ) {
			wp_mail( $to, $subject, $message, $headers, $attachments );
		}

		if ( $html_mime ) {
			remove_filter( 'wp_mail_content_type', array( $this, 'set_mail_type' ) );
		}

	}

	/**
	 * Replaces the filtered body with the unfiltered one if the notifications/email/unfiltered_html setting is set to true.
	 *
	 * @filter notification/notification/form/data/values
	 *
	 * @param  array $notification_data notification_data from PostData.
	 * @param  array $ndata             ndata from PostData, it contains the unfiltered message body.
	 * @return array $notification_data with the unfiltered body, if notifications/email/unfiltered_html setting is true.
	 **/
	public function allow_unfiltered_html_body( $notification_data, $ndata ) {

		if ( notification_get_setting( 'notifications/email/unfiltered_html' ) ) {
			$notification_data['body'] = $ndata['body'];
		}

		return $notification_data;

	}

}
