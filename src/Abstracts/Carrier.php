<?php
/**
 * Carrier abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Core\Resolver;
use BracketSpace\Notification\Defaults\Field;
use BracketSpace\Notification\Defaults\Field\RecipientsField;
use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Store\Recipient as RecipientStore;
use BracketSpace\Notification\Traits;

/**
 * Carrier abstract class
 */
abstract class Carrier implements Interfaces\Sendable {

	use Traits\ClassUtils, Traits\HasName, Traits\HasSlug;

	/**
	 * Form fields
	 *
	 * @var array
	 */
	public $form_fields = [];

	/**
	 * Recipients form field closure
	 *
	 * @var callable() : RecipientsField|null
	 */
	protected $recipients_field;

	/**
	 * Recipients form field index
	 *
	 * @var int
	 */
	public $recipients_field_index = 0;

	/**
	 * Recipients form field raw data
	 *
	 * @var mixed
	 */
	public $recipients_data;

	/**
	 * Recipients form field resolved data
	 *
	 * @var mixed
	 */
	public $recipients_resolved_data;

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
	public $restricted_fields = [ '_nonce', 'activated', 'enabled' ];

	/**
	 * If is suppressed
	 *
	 * @var boolean
	 */
	protected $suppressed = false;

	/**
	 * Carrier icon
	 *
	 * @var string SVG
	 */
	public $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 143.3 152.5"><path d="M119.8,120.8V138a69.47,69.47,0,0,1-43.2,14.5q-32.4,0-55-22.2Q-1.05,108-1,75.9c0-15.6,3.9-29.2,11.8-40.7A82,82,0,0,1,40.7,8.3,74,74,0,0,1,75.6,0a71.79,71.79,0,0,1,31,6.6,69.31,69.31,0,0,1,25.3,21.8c6.9,9.6,10.4,21.2,10.4,34.8,0,13.8-3.3,25.5-9.9,35.3s-14.3,14.7-23.1,14.7c-10.6,0-16-6.9-16-20.6V82.3C93.3,63.4,86.4,54,72.5,54c-6.2,0-11.2,2.2-14.8,6.5a23.85,23.85,0,0,0-5.4,15.8,19.46,19.46,0,0,0,6.2,14.9,21.33,21.33,0,0,0,15.1,5.7,21.75,21.75,0,0,0,13.8-4.7v16.6a27.67,27.67,0,0,1-15.5,4.3q-15.3,0-25.8-10.2t-10.5-27c0-15.5,6.8-26.7,20.4-33.8a36.74,36.74,0,0,1,17.9-4.3c12.2,0,21.7,4.5,28.5,13.6,5.2,6.9,7.9,17.4,7.9,31.5v8.5c0,3.1,1,4.7,3,4.7,3,0,5.7-3.2,8.3-9.6A56.78,56.78,0,0,0,125.4,65q0-28.95-23.6-42.9h.2c-8.1-4.3-17.4-6.4-28.1-6.4a57.73,57.73,0,0,0-28.7,7.7A58.91,58.91,0,0,0,24,45.1a61.18,61.18,0,0,0-8.2,31.5c0,17.2,5.7,31.4,17,42.7s25.7,16.9,43,16.9c9.6,0,17.5-1.2,23.6-3.5S112.3,126.5,119.8,120.8Z" transform="translate(1)"/></svg>';

	/**
	 * Carrier constructor
	 *
	 * @param string $slug Slug, optional.
	 * @param string $name Nice name, optional.
	 */
	public function __construct( $slug = null, $name = null ) {
		if ( null !== $slug ) {
			$this->set_slug( $slug );
		}

		if ( null !== $name ) {
			$this->set_name( $name );
		}

		// Form nonce.
		$nonce_field = new Field\NonceField( [
			'label'      => '',
			'name'       => '_nonce',
			'nonce_key'  => $this->get_slug() . '_carrier_security',
			'resolvable' => false,
		] );

		$nonce_field->section = 'notification_carrier_' . $this->get_slug();

		$this->form_fields[ $nonce_field->get_raw_name() ] = $nonce_field;

		// Carrier active.
		$activated_field = new Field\InputField( [
			'type'       => 'hidden',
			'label'      => '',
			'name'       => 'activated',
			'value'      => '0',
			'resolvable' => false,
			'atts'       => 'data-nt-carrier-input-active',
		] );

		$activated_field->section = 'notification_carrier_' . $this->get_slug();

		$this->form_fields[ $activated_field->get_raw_name() ] = $activated_field;

		// Carrier status.
		$enabled_field = new Field\InputField( [
			'type'       => 'hidden',
			'label'      => '',
			'name'       => 'enabled',
			'value'      => '0',
			'resolvable' => false,
			'atts'       => 'data-nt-carrier-input-enable',
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

		if ( ! $this->has_recipients_field() ) {
			$this->recipients_field_index++;
		}

		return $this;

	}

	/**
	 * Adds recipients form field
	 *
	 * @since  8.0.0
	 * @throws \Exception When recipients fields was already added.
	 * @param  array<mixed> $params Recipients field params.
	 * @return $this
	 */
	public function add_recipients_field( array $params = [] ) {
		if ( $this->has_recipients_field() ) {
			throw new \Exception( 'Recipient field has been already added' );
		}

		$this->recipients_field = function () use ( $params ) {
			return new RecipientsField( [
				'carrier' => $this->get_slug(),
			] + $params );
		};

		return $this;
	}

	/**
	 * Checks if the recipients field was added
	 *
	 * @since  8.0.0
	 * @return bool
	 */
	public function has_recipients_field() {
		return null !== $this->recipients_field;
	}

	/**
	 * Gets the recipients field
	 * Calls the field closure.
	 *
	 * @since  8.0.0
	 * @return RecipientsField|null
	 */
	public function get_recipients_field() {
		if ( ! $this->has_recipients_field() || ! is_callable( $this->recipients_field ) ) {
			return null;
		}

		$closure = $this->recipients_field;
		$field   = $closure();

		// Setup the field data if it's available.
		$this->set_field_data( $field, $this->recipients_data );

		return $field;
	}

	/**
	 * Gets the saved recipients
	 *
	 * @return mixed
	 */
	public function get_recipients() {
		$recipients_field = $this->get_recipients_field();
		return $recipients_field ? $recipients_field->get_value() : null;
	}

	/**
	 * Gets form fields array
	 *
	 * @return Interfaces\Fillable[] fields
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
		// Regular fields.
		foreach ( $this->get_form_fields() as $field ) {
			if ( ! $field->is_resolvable() ) {
				continue;
			}

			$resolved = $this->resolve_value( $field->get_value(), $trigger );
			$field->set_value( $resolved );
		}

		// Recipients field.
		if ( $this->has_recipients_field() ) {
			$recipients_field = $this->get_recipients_field();

			if ( $recipients_field ) {
				$this->recipients_resolved_data = $this->resolve_value( $recipients_field->get_value(), $trigger );
			}
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

			return $resolved;
		}

		$value = apply_filters( 'notification/carrier/field/resolving', $value );

		$resolved = Resolver::resolve( $value, $trigger );

		// Unused tags.
		$strip_merge_tags = apply_filters(
			'notification/resolve/strip_empty_mergetags',
			notification_get_setting( 'general/content/strip_empty_tags' )
		);

		if ( $strip_merge_tags ) {
			$resolved = Resolver::clear( $resolved );
		}

		// Shortcodes.
		$strip_shortcodes = apply_filters(
			'notification/carrier/field/value/strip_shortcodes',
			notification_get_setting( 'general/content/strip_shortcodes' )
		);

		if ( $strip_shortcodes ) {
			$resolved = preg_replace( '#\[[^\]]+\]#', '', $resolved );
		} else {
			$resolved = do_shortcode( $resolved );
		}

		// Unescape escaped {.
		$resolved = str_replace( '!{', '{', $resolved );

		return apply_filters( 'notification/carrier/field/value/resolved', $resolved, null );
	}

	/**
	 * Prepares saved data for easy use in send() method
	 * Saves all the values in $data property
	 *
	 * @since  5.0.0
	 * @return void
	 */
	public function prepare_data() {
		$this->data = $this->get_data();

		// If there's set recipients field, parse them into a nice array.
		// Parsed recipients are saved to key named `parsed_{recipients_field_slug}`.
		if ( $this->has_recipients_field() ) {
			$recipients_field = $this->get_recipients_field();

			if ( $recipients_field ) {
				$this->data[ 'parsed_' . $recipients_field->get_raw_name() ] = $this->parse_recipients();
			}
		}
	}

	/**
	 * Parses the recipients to a flat array.
	 *
	 * It needs recipients_resolved_data property so the
	 * resolve_fields method needs to be called beforehand.
	 *
	 * @since  8.0.0
	 * @return array<int,mixed>
	 */
	public function parse_recipients() {
		if ( ! $this->recipients_resolved_data ) {
			return [];
		}

		$parsed_recipients = [];

		foreach ( $this->recipients_resolved_data as $recipient ) {
			$parsed_recipients = array_merge(
				$parsed_recipients,
				(array) RecipientStore::get( $this->get_slug(), $recipient['type'] )->parse_value( $recipient['recipient'] ) ?? []
			);
		}

		// Remove duplicates.
		return array_unique( $parsed_recipients );
	}

	/**
	 * Sets data from array
	 *
	 * @since  6.0.0
	 * @param  array $data Data with keys matched with Field names.
	 * @return $this
	 */
	public function set_data( $data ) {
		// Set fields data.
		foreach ( $this->get_form_fields() as $field ) {
			if ( isset( $data[ $field->get_raw_name() ] ) ) {
				$this->set_field_data( $field, $data[ $field->get_raw_name() ] );
			}
		}

		// Set recipients data.
		if ( $this->has_recipients_field() ) {
			$recipients_field = $this->get_recipients_field();
			if ( $recipients_field && isset( $data[ $recipients_field->get_raw_name() ] ) ) {
				$this->recipients_data = $data[ $recipients_field->get_raw_name() ];
			}
		}

		return $this;
	}

	/**
	 * Sets field data
	 *
	 * @since  8.0.0
	 * @param  Interfaces\Fillable $field Field.
	 * @param  mixed               $data  Field data.
	 * @return void
	 */
	protected function set_field_data( Interfaces\Fillable $field, $data ) {
		$field->set_value( $field->sanitize( $data ) );
	}

	/**
	 * Gets data
	 *
	 * @since  6.0.0
	 * @return array
	 */
	public function get_data() {
		$data = [];

		// Get fields data.
		foreach ( $this->get_form_fields() as $field ) {
			if ( ! $field instanceof Field\NonceField ) {
				$data[ $field->get_raw_name() ] = $field->get_value();
			}
		}

		// Get recipients data.
		if ( $this->has_recipients_field() ) {
			$recipients_field = $this->get_recipients_field();

			if ( $recipients_field ) {
				$data[ $recipients_field->get_raw_name() ] = $recipients_field->get_value();
			}
		}

		return $data;
	}

	/**
	 * Checks if Carrier is active
	 *
	 * @since  6.3.0
	 * @return bool
	 */
	public function is_active() {
		return ! empty( $this->get_field_value( 'activated' ) );
	}

	/**
	 * Activates the Carrier
	 *
	 * @since  6.3.0
	 * @return $this
	 */
	public function activate() {
		$this->get_form_field( 'activated' )->set_value( true );
		return $this;
	}

	/**
	 * Deactivates the Carrier
	 *
	 * @since  6.3.0
	 * @return $this
	 */
	public function deactivate() {
		$this->get_form_field( 'activated' )->set_value( false );
		return $this;
	}

	/**
	 * Checks if Carrier is enabled
	 *
	 * @since  6.0.0
	 * @return bool
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
	 * Disables the Carrier
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
