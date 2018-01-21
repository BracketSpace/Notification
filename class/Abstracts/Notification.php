<?php

namespace underDEV\Notification\Abstracts;
use underDEV\Notification\Interfaces;
use underDEV\Notification\Defaults\Field;

abstract class Notification extends Common implements Interfaces\Sendable {

	/**
	 * If Notification is enabled for current post
	 * @var boolean
	 */
	public $enabled = false;

	/**
	 * Notification form fields
	 * @var array
	 */
	public $form_fields = array();

	public function __construct( $slug, $name ) {

		$this->slug = $slug;
		$this->name = $name;

		$this->add_form_field( new Field\NonceField( array(
			'label'      => '',
			'name'       => 'nonce',
			'nonce_key'  => $this->slug . '_notification_security',
			'resolvable' => false,
		) ) );

		$this->form_fields();

	}

	/**
	 * Used to register notification form fields
	 * Uses $this->add_form_field();
	 * @return void
	 */
	abstract public function form_fields();

	/**
	 * Sends the notification
	 * @param  \underDEV\Notification\Abstracts\Trigger $trigger trigger objecy
	 * @return void
	 */
	abstract public function send( \underDEV\Notification\Abstracts\Trigger $trigger );

	/**
	 * Generates an unique hash for notification instance
	 * @return string
	 */
	public function hash() {
		return md5( json_encode( $this ) );
	}

	/**
	 * Adds form field to collection
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
	 * @return array fields
	 */
	public function get_form_fields() {
		return $this->form_fields;
	}

	/**
	 * Gets field value
	 * @param  string $field_slug field slug
	 * @return mixed              value or null if field not available
	 */
	public function get_field_value( $field_slug ) {

		if ( ! isset( $this->form_fields[ $field_slug ] ) ) {
			return null;
		}

		return $this->form_fields[ $field_slug ]->get_value();

	}

}
