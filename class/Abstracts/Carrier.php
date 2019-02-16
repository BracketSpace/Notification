<?php
/**
 * Carrier abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Defaults\Field;
use BracketSpace\Notification\Defaults\Field\RecipientsField;

/**
 * Carrier abstract class
 */
abstract class Carrier extends Common implements Interfaces\Sendable {

	/**
	 * If Carrier is enabled
	 *
	 * @var boolean
	 */
	public $enabled = false;

	/**
	 * Carrier form fields
	 *
	 * @var array
	 */
	public $form_fields = [];

	/**
	 * Fields data for send method
	 *
	 * @var array
	 */
	public $data = [];

	/**
	 * If is suppressed
	 *
	 * @var boolean
	 */
	protected $suppressed = false;

	/**
	 * Current Carrier post ID
	 *
	 * @var integer
	 */
	public $post_id = 0;

	/**
	 * Carrier constructor
	 *
	 * @param string $slug slug.
	 * @param string $name nice name.
	 */
	public function __construct( $slug, $name ) {

		$this->slug = $slug;
		$this->name = $name;

		$this->add_form_field( new Field\NonceField( [
			'label'      => '',
			'name'       => '_nonce',
			'nonce_key'  => $this->slug . '_notification_security',
			'resolvable' => false,
		] ) );

		$this->form_fields();

	}

	/**
	 * Clone method
	 * Copies the fields to new Carrier instance
	 *
	 * @since  5.1.6
	 * @return void
	 */
	public function __clone() {

		$fields = [];

		foreach ( $this->form_fields as $raw_name => $field ) {
			$fields[ $raw_name ] = clone $field;
		}

		$this->form_fields = $fields;

	}

	/**
	 * Used to register Carrier form fields
	 * Uses $this->add_form_field();
	 *
	 * @return void
	 */
	abstract public function form_fields();

	/**
	 * Sends the Carrier
	 *
	 * @param  Triggerable $trigger trigger object.
	 * @return void
	 */
	abstract public function send( Triggerable $trigger );

	/**
	 * Generates an unique hash for Carrier instance
	 *
	 * @return string
	 */
	public function hash() {
		return md5( wp_json_encode( $this ) );
	}

	/**
	 * Adds form field to collection
	 *
	 * @param  Interfaces\Fillable $field Field object.
	 * @return $this
	 */
	public function add_form_field( Interfaces\Fillable $field ) {
		$adding_field                                = clone $field;
		$adding_field->section                       = 'notification_type_' . $this->get_slug();
		$this->form_fields[ $field->get_raw_name() ] = $adding_field;
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
	 * Gets form fields array
	 *
	 * @since  [Next]
	 * @param  string $field_name Field name.
	 * @return mixed              Field object or null.
	 */
	public function get_form_field( $field_name ) {
		return isset( $this->form_fields[ $field_name ] ) ? $this->form_fields[ $field_name ] : null;
	}

	/**
	 * Gets field value
	 *
	 * @param  string $field_slug field slug.
	 * @return mixed              value or null if field not available
	 */
	public function get_field_value( $field_slug ) {

		if ( ! isset( $this->form_fields[ $field_slug ] ) ) {
			return null;
		}

		return $this->form_fields[ $field_slug ]->get_value();

	}

	/**
	 * Prepares saved data for easy use in send() method
	 * Saves all the values in $data property
	 *
	 * @since  5.0.0
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
			if ( $field->get_raw_name() === '_nonce' ) {
				continue;
			}

			$this->data[ $field_slug ] = $this->get_field_value( $field_slug );

		}

		// If there's set recipients field, parse them into a nice array.
		// Parsed recipients are saved to key named `parsed_{recipients_field_slug}`.
		if ( $recipients_field ) {

			$parsed_recipients = [];

			$raw_recipients = $this->get_field_value( $recipients_field->get_raw_name() );

			foreach ( $raw_recipients as $recipient ) {
				$type_recipients   = notification_parse_recipient( $this->get_slug(), $recipient['type'], $recipient['recipient'] );
				$parsed_recipients = array_merge( $parsed_recipients, (array) $type_recipients );
			}

			// Remove duplicates and save to data property.
			$this->data[ 'parsed_' . $recipients_field->get_raw_name() ] = array_unique( $parsed_recipients );

		}

	}

	/**
	 * Sets data from array
	 *
	 * @since  [Next]
	 * @param  array $data Data with keys matched with Field names.
	 * @return $this
	 */
	public function set_data( $data ) {

		foreach ( $this->get_form_fields() as $field ) {
			if ( isset( $data[ $field->get_raw_name() ] ) ) {
				$field->set_value( $field->sanitize( $data[ $field->get_raw_name() ] ) );
			}
		}

		return $this;

	}

	/**
	 * Gets data
	 *
	 * @since  [Next]
	 * @return array
	 */
	public function get_data() {

		$data = [];

		foreach ( $this->get_form_fields() as $field ) {
			if ( ! $field instanceof Field\NonceField ) {
				$data[ $field->get_raw_name() ] = $field->get_value();
			}
		}

		return $data;

	}

	/**
	 * Checks if Carrier is suppressed
	 *
	 * @since  5.1.2
	 * @return boolean
	 */
	public function is_suppressed() {
		return $this->suppressed;
	}

	/**
	 * Suppresses the Carrier
	 *
	 * @since  5.1.2
	 * @return void
	 */
	public function suppress() {
		$this->suppressed = true;
	}

}
