<?php
/**
 * Input field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Input field class
 */
class InputField extends Field {

	/**
	 * Field type
	 * possible values are valid HTML5 types except file or checkbox
	 *
	 * @var string
	 */
	public $type = 'text';

	/**
	 * Field placeholder
	 *
	 * @var string
	 */
	protected $placeholder = '';

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = array() ) {

		if ( isset( $params['type'] ) ) {
			$this->type = $params['type'];
		}

		if ( isset( $params['placeholder'] ) ) {
			$this->placeholder = $params['placeholder'];
		}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field() {
		return '<input type="' . esc_attr( $this->type ) . '" name="' . esc_attr( $this->get_name() ) . '" id="' . esc_attr( $this->get_id() ) . '" value="' . esc_attr( $this->get_value() ) . '" placeholder="' . esc_attr( $this->placeholder ) . '" class="widefat ' . esc_attr( $this->css_class() ) . '" ' . $this->maybe_disable() . '>';
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value ) {
		return sanitize_text_field( $value );
	}

}
