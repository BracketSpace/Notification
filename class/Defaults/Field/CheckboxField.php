<?php
/**
 * Checkbox field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Checkbox field class
 */
class CheckboxField extends Field {

	/**
	 * Checkbox label text
	 * Default: Enable
	 *
	 * @var string
	 */
	protected $checkbox_label = '';

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] ) {

		if ( isset( $params['checkbox_label'] ) ) {
			$this->checkbox_label = $params['checkbox_label'];
		} else {
			$this->checkbox_label = __( 'Enable', 'notification' );
		}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field() {
		return '<label><input type="checkbox" name="' . esc_attr( $this->get_name() ) . '" id="' . esc_attr( $this->get_id() ) . '" value="1" ' . checked( $this->get_value(), '1', false ) . ' class="widefat ' . esc_attr( $this->css_class() ) . '" ' . $this->maybe_disable() . '> ' . esc_html( $this->checkbox_label ) . '</label>';
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value ) {
		return $value ? 1 : 0;
	}

}
