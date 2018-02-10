<?php
/**
 * Notification abstract class
 *
 * @package notification
 */

namespace underDEV\Notification\Abstracts;

use underDEV\Notification\Interfaces;
use underDEV\Notification\Defaults\Field;
use underDEV\Notification\Defaults\Field\RecipientsField;

abstract class Notification extends Common implements Interfaces\Sendable {

	/**
	 * If Notification is enabled for current post
     *
	 * @var boolean
	 */
	public $enabled = false;

	/**
	 * Notification form fields
     *
	 * @var array
	 */
	public $form_fields = array();

	/**
	 * Fields data for send method
     *
	 * @var array
	 */
	public $data = array();

	public function __construct( $slug, $name ) {

		$this->slug = $slug;
		$this->name = $name;

		$this->add_form_field( new Field\NonceField( array(
			'label'      => '',
			'name'       => '_nonce',
			'nonce_key'  => $this->slug . '_notification_security',
			'resolvable' => false,
		) ) );

		$this->form_fields();

	}

	/**
	 * Used to register notification form fields
	 * Uses $this->add_form_field();
     *
	 * @return void
	 */
	abstract public function form_fields();

	/**
	 * Sends the notification
     *
	 * @param  \underDEV\Notification\Abstracts\Trigger $trigger trigger objecy
	 * @return void
	 */
	abstract public function send( \underDEV\Notification\Abstracts\Trigger $trigger );

	/**
	 * Generates an unique hash for notification instance
     *
	 * @return string
	 */
	public function hash() {
		return md5( json_encode( $this ) );
	}

	/**
	 * Adds form field to collection
     *
	 * @param  object $field Field object
	 * @return $this
	 */
	public function add_form_field( Interfaces\Fillable $field ) {
		$field->section = 'notification_type_' . $this->get_slug();
		$this->form_fields[ $field->get_raw_name() ] = clone $field;
		return $this;
	}

	/**
	 * Gets form fields array
     *
	 * @return array fields
	 */
	public function get_form_fields() {
		return $this->form_fields;
	}

	/**
	 * Gets field value
     *
	 * @param  string $field_slug field slug
	 * @return mixed              value or null if field not available
	 */
	public function get_field_value( $field_slug ) {

		if ( ! isset( $this->form_fields[ $field_slug ] ) ) {
			return null;
		}

		return $this->form_fields[ $field_slug ]->get_value();

	}

	/**
	 * Prepares saved data for easy use in send() methor
	 * Saves all the values in $data property
	 *
	 * @since  [Unreleased]
	 * @return void
	 */
	public function prepare_data() {

		$recipients_field = false;

		foreach ( $this->get_form_fields() as $field_slug => $field ) {

			// Save recipients field for additional parsing.
			if ( $field instanceof RecipientsField ) {
				$recipients_field = $field;
			}

			// Skip internal nonce field.
			if ( $field->get_raw_name() == '_nonce' ) {
				continue;
			}

			$this->data[ $field_slug ] = $this->get_field_value( $field_slug );

		}

		// If there's set recipients field, parse them into a nice array.
		// Parsed recipients are saved to key named `parsed_{recipients_field_slug}`
		if ( $recipients_field ) {

			$parsed_recipients = array();

			$raw_recipients = $this->get_field_value( $recipients_field->get_raw_name() );

			foreach ( $raw_recipients as $recipient ) {
				$type_recipients   = notification_parse_recipient( $this->get_slug(), $recipient['type'], $recipient['recipient'] );
				$parsed_recipients = array_merge( $parsed_recipients, (array) $type_recipients );
			}

			// Remove duplicates and save to data property.
			$this->data[ 'parsed_' . $recipients_field->get_raw_name() ] = array_unique( $parsed_recipients );

		}

	}

}
