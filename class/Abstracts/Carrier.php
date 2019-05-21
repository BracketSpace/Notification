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
	 * Restricted form field keys
	 *
	 * @var array
	 */
	public $restricted_fields = [ '_nonce', 'enabled' ];

	/**
	 * If is suppressed
	 *
	 * @var boolean
	 */
	protected $suppressed = false;

	/**
	 * Carrier constructor
	 *
	 * @param string $slug slug.
	 * @param string $name nice name.
	 */
	public function __construct( $slug, $name ) {

		$this->slug = $slug;
		$this->name = $name;

		// Form nonce.
		$nonce_field = new Field\NonceField( [
			'label'      => '',
			'name'       => '_nonce',
			'nonce_key'  => $this->get_slug() . '_carrier_security',
			'resolvable' => false,
		] );

		$nonce_field->section = 'notification_carrier_' . $this->get_slug();

		$this->form_fields[ $nonce_field->get_raw_name() ] = $nonce_field;

		// Carrier status.
		$enabled_field = new Field\InputField( [
			'type'       => 'hidden',
			'label'      => '',
			'name'       => 'enabled',
			'value'      => '0',
			'resolvable' => false,
		] );

		$enabled_field->section = 'notification_carrier_' . $this->get_slug();

		$this->form_fields[ $enabled_field->get_raw_name() ] = $enabled_field;

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
	 * @since  6.0.0 Added restricted field check.
	 * @throws \Exception When restricted name is used.
	 * @param  Interfaces\Fillable $field Field object.
	 * @return $this
	 */
	public function add_form_field( Interfaces\Fillable $field ) {

		if ( in_array( $field->get_raw_name(), $this->restricted_fields, true ) ) {
			throw new \Exception( 'You cannot use restricted field name. Restricted names: ' . implode( ', ', $this->restricted_fields ) );
		}

		$adding_field          = clone $field;
		$adding_field->section = 'notification_carrier_' . $this->get_slug();

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
	 * @since  6.0.0
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
	 * Resolves all fields
	 *
	 * @since  6.0.0
	 * @param  Triggerable $trigger Trigger object.
	 * @return void
	 */
	public function resolve_fields( Triggerable $trigger ) {

		foreach ( $this->get_form_fields() as $field ) {

			if ( ! $field->is_resolvable() ) {
				continue;
			}

			$resolved = $this->resolve_value( $field->get_value(), $trigger );
			$field->set_value( $resolved );

		}

	}

	/**
	 * Resolves Merge Tags in field value
	 *
	 * @since 6.0.0
	 * @param  mixed       $value   String or array, field value.
	 * @param  Triggerable $trigger Trigger object.
	 * @return mixed
	 */
	protected function resolve_value( $value, Triggerable $trigger ) {

		if ( is_array( $value ) ) {
			$resolved = [];

			foreach ( $value as $key => $val ) {
				$key              = $this->resolve_value( $key, $trigger );
				$val              = $this->resolve_value( $val, $trigger );
				$resolved[ $key ] = $val;
			}
		} else {

			$value = apply_filters_deprecated( 'notificaiton/notification/field/resolving', [
				$value,
				null,
			], '6.0.0', 'notification/carrier/field/resolving' );
			$value = apply_filters( 'notification/carrier/field/resolving', $value, null );

			$resolved = notification_resolve( $value, $trigger );

			// Unused tags.
			$strip_merge_tags = notification_get_setting( 'general/content/strip_empty_tags' );
			$strip_merge_tags = apply_filters_deprecated( 'notification/value/strip_empty_mergetags', [
				$strip_merge_tags,
			], '6.0.0', 'notification/resolve/strip_empty_mergetags' );
			$strip_merge_tags = apply_filters( 'notification/resolve/strip_empty_mergetags', $strip_merge_tags );

			if ( $strip_merge_tags ) {
				$resolved = notification_clear_tags( $resolved );
			}

			// Shortcodes.
			$strip_shortcodes = notification_get_setting( 'general/content/strip_shortcodes' );
			$strip_shortcodes = apply_filters_deprecated( 'notification/value/strip_shortcodes', [
				$strip_shortcodes,
			], '6.0.0', 'notification/carrier/field/value/strip_shortcodes' );

			if ( apply_filters( 'notification/carrier/field/value/strip_shortcodes', $strip_shortcodes ) ) {
				$resolved = preg_replace( '#\[[^\]]+\]#', '', $resolved );
			} else {
				$resolved = do_shortcode( $resolved );
			}

			// Unescape escaped {.
			$resolved = str_replace( '!{', '{', $resolved );

			$resolved = apply_filters_deprecated( 'notificaiton/notification/field/resolved', [
				$resolved,
				null,
			], '6.0.0', 'notification/carrier/field/value/resolved' );
			$resolved = apply_filters( 'notification/carrier/field/value/resolved', $resolved, null );

		}

		return $resolved;

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

		// Save recipients field for additional parsing.
		foreach ( $this->get_form_fields() as $field ) {
			if ( $field instanceof RecipientsField ) {
				$recipients_field = $field;
				continue;
			}
		}

		$this->data = $this->get_data();

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
	 * @since  6.0.0
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
	 * @since  6.0.0
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
	 * Checks if Carrier is enabled
	 *
	 * @since  6.0.0
	 * @return boolean
	 */
	public function is_enabled() {
		return ! empty( $this->get_field_value( 'enabled' ) );
	}

	/**
	 * Enables the Carrier
	 *
	 * @since  6.0.0
	 * @return $this
	 */
	public function enable() {
		$this->get_form_field( 'enabled' )->set_value( true );
		return $this;
	}

	/**
	 * Disabled the Carrier
	 *
	 * @since  6.0.0
	 * @return $this
	 */
	public function disable() {
		$this->get_form_field( 'enabled' )->set_value( false );
		return $this;
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
