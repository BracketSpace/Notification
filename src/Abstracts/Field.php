<?php
/**
 * Field abstract class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Abstracts;

use BracketSpace\Notification\Interfaces;

/**
 * Field abstract class
 */
abstract class Field implements Interfaces\Fillable {

	/**
	 * Field unique ID
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Field value
	 *
	 * @var mixed
	 */
	public $value;

	/**
	 * Field label
	 *
	 * @var mixed
	 */
	protected $label;

	/**
	 * Field name
	 *
	 * @var mixed
	 */
	protected $name;

	/**
	 * Short description
	 * Limited HTML support
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * If field is resolvable with merge tags
	 * Default: true
	 *
	 * @var boolean
	 */
	protected $resolvable = true;

	/**
	 * Field section name
	 *
	 * @var string
	 */
	public $section = '';

	/**
	 * If field is disabled
	 *
	 * @var boolean
	 */
	public $disabled = false;

	/**
	 * Additional css classes for field
	 *
	 * @var string
	 */
	public $css_class = 'widefat notification-field '; // space here on purpose.

	/**
	 * If field can be used multiple times in Section Repeater row
	 *
	 * @var  boolean
	 */
	public $multiple_section = false;

	/**
	 * Field type used in HTML attribute.
	 *
	 * @var string
	 */
	public $field_type_html = '';

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @param array $params field configuration params.
	 */
	public function __construct( $params = [] ) {

		if ( ! isset( $params['label'], $params['name'] ) ) {
			trigger_error( 'Field requires label and name', E_USER_ERROR );
		}

		$this->field_type_html = substr( strrchr( get_called_class(), '\\' ), 1 );

		$this->label = $params['label'];
		$this->name  = $params['name'];
		$this->id    = $this->name . '_' . uniqid();

		if ( isset( $params['description'] ) ) {
			$this->description = wp_kses( $params['description'], wp_kses_allowed_html( 'data' ) );
		}

		if ( isset( $params['resolvable'] ) ) {
			$this->resolvable = (bool) $params['resolvable'];
		}

		if ( isset( $params['value'] ) ) {
			$this->set_value( $params['value'] );
		}

		if ( isset( $params['disabled'] ) && $params['disabled'] ) {
			$this->disabled = true;
		}

		if ( isset( $params['css_class'] ) ) {
			$this->css_class .= $params['css_class'];
		}

		if ( isset( $params['multiple_section'] ) ) {
			$this->multiple_section = $params['multiple_section'];
		}

	}

	/**
	 * Returns field data
	 *
	 * @since 7.0.0
	 * @param string $param Field data name.
	 * @return  array
	 */
	public function __get( $param ) {
		return $this->$param ?? null;
	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	abstract public function field();

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	abstract public function sanitize( $value );

	/**
	 * Gets description
	 *
	 * @return string description
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Gets field value
	 *
	 * @return mixed
	 */
	public function get_value() {
		$value = is_string( $this->value ) ? stripslashes( $this->value ) : $this->value;
		return apply_filters( 'notification/field/' . $this->get_raw_name() . '/value', $value, $this );
	}

	/**
	 * Sets field value
	 *
	 * @param  mixed $value value from DB.
	 * @return void
	 */
	public function set_value( $value ) {
		$this->value = $value;
	}

	/**
	 * Gets field name
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->section . '[' . $this->name . ']';
	}

	/**
	 * Gets field raw name
	 *
	 * @return string
	 */
	public function get_raw_name() {
		return $this->name;
	}

	/**
	 * Gets field label
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Gets field ID
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Cheks if field should be resolved with merge tags
	 *
	 * @return bool
	 */
	public function is_resolvable() {
		return $this->resolvable;
	}

	/**
	 * Cheks if field is disabled
	 *
	 * @return bool
	 */
	public function is_disabled() {
		return $this->disabled;
	}

	/**
	 * Returns the disable HTML tag if field is disabled
	 *
	 * @return string
	 */
	public function maybe_disable() {
		return $this->is_disabled() ? 'disabled="disabled"' : '';
	}

	/**
	 * Returns the additional field's css classes
	 *
	 * @return string
	 */
	public function css_class() {
		return $this->css_class;
	}

	/**
	 * Returns rest API error message
	 *
	 * @since 7.1.0
	 * @return string
	 */
	public function rest_api_error() {
		return esc_html__( 'The REST API is required to display this field, but it has been blocked. Please unlock the /notification REST API endpoint.', 'notification' );
	}

}
