<?php
/**
 * Email notification
 */

namespace underDEV\Notification\Defaults\Notification;

use underDEV\Notification\Abstracts;
use underDEV\Notification\Defaults\Field;

class Email extends Abstracts\Notification {

	public function __construct() {
		parent::__construct( 'email', 'Email' );
	}

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

	public function send( \underDEV\Notification\Abstracts\Trigger $trigger ) {

		$html_mime = apply_filters( 'notification/' . $this->get_slug() . '/use_html_mime', true, $this, $trigger );

		if ( $html_mime ) {
			add_filter( 'wp_mail_content_type', array( $this, 'set_mail_type' ) );
		}

		$data = $this->data;

		$recipients = apply_filters( 'notification/' . $this->get_slug() . '/recipients', $data['parsed_recipients'], $this, $trigger );

		$subject = apply_filters( 'notification/' . $this->get_slug() . '/subject/pre', $data['subject'], $this, $trigger );
		if ( apply_filters( 'notification/' . $this->get_slug() . '/subject/strip_shortcodes', true, $this, $trigger ) ) {
			$subject = strip_shortcodes( $subject );
		}
		$subject = apply_filters( 'notification/' . $this->get_slug() . '/subject', $subject, $this, $trigger );

		$message = apply_filters( 'notification/' . $this->get_slug() . '/message/pre', $data['body'], $this, $trigger );
		if ( apply_filters( 'notification/' . $this->get_slug() . '/message/strip_shortcodes', true, $this, $trigger ) ) {
			$message = strip_shortcodes( $message );
		}
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
