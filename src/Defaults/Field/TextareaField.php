<?php
/**
 * Textarea field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Textarea field class
 */
class TextareaField extends Field {

	/**
	 * Field placeholder
	 *
	 * @var string
	 */
	protected $placeholder = '';

	/**
	 * Textarea rows
	 *
	 * @var integer
	 */
	protected $rows = 10;

	/**
	 * If unfiltered value is allowed
	 *
	 * @var boolean
	 */
	protected $allowed_unfiltered = false;

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] ) {

		if ( isset( $params['placeholder'] ) ) {
			$this->placeholder = $params['placeholder'];
		}

		if ( isset( $params['rows'] ) ) {
			$this->rows = $params['rows'];
		}

		if ( isset( $params['allowed_unfiltered'] ) && $params['allowed_unfiltered'] ) {
			$this->allowed_unfiltered = true;
		}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field() {
		return '<textarea name="' . esc_attr( $this->get_name() ) . '" rows="' . esc_attr( (string) $this->rows ) . '" id="' . esc_attr( $this->get_id() ) . '" placeholder="' . esc_attr( $this->placeholder ) . '" class="widefat ' . esc_attr( $this->css_class() ) . '" ' . $this->maybe_disable() . '>' . $this->get_value() . '</textarea>';
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value ) {
		return ( $this->allowed_unfiltered ) ? $value : sanitize_textarea_field( $value );
	}

}
