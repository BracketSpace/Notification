<?php

namespace underDEV\Notification\Defaults\Field;
use underDEV\Notification\Abstracts\Field;

class RepeaterField extends Field {

	/**
	 * Current repeater row
	 * @var integer
	 */
	protected $current_row = 0;

	/**
	 * Fields to repeat
	 * @var string
	 */
	protected $fields = array();

	/**
	 * Add new button label
	 * @var string
	 */
	protected $add_button_label = '';

	public function __construct( $params = array() ) {

		if ( isset( $params['fields'] ) ) {
    		$this->fields = $params['fields'];
    	}

		if ( isset( $params['add_button_label'] ) ) {
    		$this->add_button_label = $params['add_button_label'];
    	} else {
    		$this->add_button_label = __( 'Add new', 'notification' );
    	}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 * @return string html
	 */
	public function field() {

		$html = '<table class="fields-repeater" id="' . $this->get_id() . '">';

			$html .= '<tr class="row header">';

				$html .= '<th class="handle"></th>';

				foreach ( $this->fields as $sub_field ) {
					$html .= '<th class="' . esc_attr( $sub_field->get_raw_name() ) . '">';
						$html .= esc_html( $sub_field->get_label() );
					$html .= '</th>';
				}

			$html .= '</tr>';

			$html .= $this->row( array(), true );

			if ( ! empty( $this->value ) ) {

				foreach ( $this->value as $row_values ) {
					$html .= $this->row( $row_values );
					$this->current_row++;
				}

			}

		$html .= '</table>';

		$html .= '<a href="#" class="button button-secondary add-new-repeater-field">' . esc_html( $this->add_button_label ) . '</a>';

		return $html;

	}

	public function row( $values = array(), $model = false ) {

		$html = '';

		if ( $model ) {
			$html .= '<tr class="row model">';
		} else {
			$html .= '<tr class="row">';
		}

			$html .= '<td class="handle"></td>';

			foreach ( $this->fields as $sub_field ) {

				if ( isset( $values[ $sub_field->get_raw_name() ] ) ) {
					$sub_field->set_value( $values[ $sub_field->get_raw_name() ] );
				}

				$sub_field->section = $this->get_name() . '[' . $this->current_row . ']';

				$html .= '<td class="subfield ' . esc_attr( $sub_field->get_raw_name() ) . '">';
					$html .= $sub_field->field();
				$html .= '</td>';

			}

		$html .= '</tr>';

		return $html;

	}

	/**
     * Sanitizes the value sent by user
     * @param  mixed $value value to sanitize
     * @return mixed        sanitized value
     */
    public function sanitize( $value ) {

    	if ( empty( $value ) ) {
    		return array();
    	}

    	$sanitized = array();

    	foreach ( $value as $row_id => $row ) {

    		$sanitized[ $row_id ] = array();

    		foreach ( $this->fields as $sub_field ) {

    			$subkey = $sub_field->get_raw_name();
    			$sanitized[ $row_id ][ $subkey ] = $sub_field->sanitize( $row[ $subkey ] );

    		}

    	}

    	return $sanitized;

    }

}
