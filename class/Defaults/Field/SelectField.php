<?php

namespace underDEV\Notification\Defaults\Field;
use underDEV\Notification\Abstracts\Field;

class SelectField extends Field {

	/**
	 * Field options
	 * value => label array
	 * @var string
	 */
	protected $options = array();

	public function __construct( $params = array() ) {

		if ( isset( $params['options'] ) ) {
    		$this->options = $params['options'];
    	}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 * @return string html
	 */
	public function field() {

		$html = '<select name="' . $this->get_name() . '" id="' . $this->get_id() . '" class="widefat">';

			foreach ( $this->options as $option_value => $option_label ) {
				$html .= '<option value="' . esc_attr( $option_value ) . '" ' . selected( $this->get_value(), $option_value, false ) . '>' . esc_html( $option_label ) . '</option>';
			}

		$html .= '</select>';

		return $html;

	}

	/**
     * Sanitizes the value sent by user
     * @param  mixed $value value to sanitize
     * @return mixed        sanitized value
     */
    public function sanitize( $value ) {
    	return sanitize_text_field( $value );
    }

}
