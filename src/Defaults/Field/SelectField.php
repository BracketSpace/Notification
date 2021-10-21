<?php
/**
 * Select field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Select field class
 */
class SelectField extends Field {

	/**
	 * Field options
	 * value => label array
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * Class for pretty select
	 * Will be used by JS to print Selectize input
	 *
	 * @var string
	 */
	protected $pretty = '';

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] ) {

		if ( isset( $params['options'] ) ) {
			$this->options = $params['options'];
		}

		if ( isset( $params['pretty'] ) && $params['pretty'] ) {
			$this->pretty = 'notification-pretty-select';
		}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field() {

		$css_classes = $this->pretty . ' ' . $this->css_class();

		$html = '<select name="' . esc_attr( $this->get_name() ) . '" id="' . esc_attr( $this->get_id() ) . '" class="' . $css_classes . '" ' . $this->maybe_disable() . '>';

		foreach ( $this->options as $option_value => $option_label ) {
			$html .= '<option value="' . esc_attr( $option_value ) . '" ' . selected( $this->get_value(), $option_value, false ) . '>' . esc_html( $option_label ) . '</option>';
		}

		$html .= '</select>';

		return $html;

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
