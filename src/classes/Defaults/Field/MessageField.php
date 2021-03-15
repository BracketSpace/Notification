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
class MessageField extends Field {

	/**
	 * Message
	 *
	 * @var string
	 */
	protected $message = '';

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * Field placeholder
	 *
	 * @var string
	 */
	protected $placeholder = '';

	/**
	 * Field attributes
	 *
	 * @var string
	 */
	protected $atts = '';

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @since 6.3.1 Allow for whitespace characters.
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] ) {

		if ( ! isset( $params['message'] ) ) {
			trigger_error( 'MessageField requires message param', E_USER_ERROR );
		}

		$this->message = $params['message'];

		if ( isset( $params['type'] ) ) {
			$this->type = $params['type'];
		}

		if ( isset( $params['name'] ) ) {
			$this->name = $params['name'];
		}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field() {
		return '<input type="' . esc_attr( $this->type ) . '" name="' . esc_attr( $this->get_name() ) . '" id="' . esc_attr( $this->get_id() ) . '" value="' . esc_attr( $this->get_value() ) . '" placeholder="' . esc_attr( $this->placeholder ) . '" class="widefat ' . esc_attr( $this->css_class() ) . '" ' . $this->maybe_disable() . ' ' . esc_attr( $this->atts ) . '>';
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value ) {

		$value = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $value ); // Remove script and style tags.

		$value = trim( $value ); // Remove whitespace.
		return $value;
	}

}
