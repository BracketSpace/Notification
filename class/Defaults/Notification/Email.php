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
		parent::__construct( 'email', 'Email' );
	}

	/**
	 * Used to register notification form fields
	 * Uses $this->add_form_field();
     *
	 * @return void
	 */
	public function form_fields() {

		$this->add_form_field( new Field\InputField( array(
			'label' => 'Subject',
			'name'  => 'subject',
		) ) );

		$this->add_form_field( new Field\EditorField( array(
			'label'    => 'Body',
			'name'     => 'body',
			'settings' => array(
				'media_buttons' => false
			)
		) ) );

		$this->add_form_field( new Field\RecipientsField( array(
			'notification' => $this->get_slug(),
		) ) );

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

		$html_mime = apply_filters( 'notification/' . $this->get_slug() . '/use_html_mime', true, $this, $trigger );

		if ( $html_mime ) {
			add_filter( 'wp_mail_content_type', array( $this, 'set_mail_type' ) );
		}

		$data = $this->data;

		$recipients = apply_filters( 'notification/' . $this->get_slug() . '/recipients', $data['parsed_recipients'], $this, $trigger );

		$subject = apply_filters( 'notification/' . $this->get_slug() . '/subject', $data['subject'], $this, $trigger );

		$message = apply_filters( 'notification/' . $this->get_slug() . '/message/pre', $data['body'], $this, $trigger );
		if ( apply_filters( 'notification/' . $this->get_slug() . '/message/use_autop', true, $this, $trigger ) ) {
			$message = wpautop( $message );
		}
		$message = apply_filters( 'notification/' . $this->get_slug() . '/message', $message, $this, $trigger );

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

}
