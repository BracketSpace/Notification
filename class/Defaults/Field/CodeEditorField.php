<?php
/**
 * Code Editor field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Editor field class
 */
class CodeEditorField extends Field {

	/**
	 * Editor settings
	 *
	 * @see https://codex.wordpress.org/Function_Reference/wp_editor#Arguments
	 * @var string
	 */
	protected $settings = 'text';

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] ) {

		if ( isset( $params['settings'] ) ) {
			$this->settings = $params['settings'];
		}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field() {
		return '<textarea id="' . esc_attr( $this->get_id() ) . '" class="widefat widefat notification-field" rows="10" name="' . esc_attr( $this->get_name() ) . '">' . esc_attr( $this->get_value() ) . '</textarea>';

	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value ) {
		return wp_kses_post( $value );
	}

}
