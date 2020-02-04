<?php
/**
 * Repeater field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Repeater field class
 */
class RepeaterField extends Field {

	/**
	 * Current repeater row
	 *
	 * @var integer
	 */
	protected $current_row = 0;

	/**
	 * Fields to repeat
	 *
	 * @var string
	 */
	protected $fields = [];

	/**
	 * Add new button label
	 *
	 * @var string
	 */
	protected $add_button_label = '';

	/**
	 * Data attributes
	 *
	 * @var array
	 */
	protected $data_attr = [];

	/**
	 * Row headers
	 *
	 * @var array
	 */
	protected $headers = [];

	/**
	 * If table is sortable
	 *
	 * @var array
	 */
	protected $sortable = true;

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] ) {

		if ( isset( $params['fields'] ) ) {
			$this->fields = $params['fields'];
		}

		if ( isset( $params['add_button_label'] ) ) {
			$this->add_button_label = $params['add_button_label'];
		} else {
			$this->add_button_label = __( 'Add new', 'notification' );
		}

		// additional data tags for repeater table. key => value array.
		// will be transformed to data-key="value".
		if ( isset( $params['data_attr'] ) ) {
			$this->data_attr = $params['data_attr'];
		}

		if ( isset( $params['sortable'] ) && ! $params['sortable'] ) {
			$this->sortable = false;
		}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field() {

		$data_attr = '';
		foreach ( $this->data_attr as $key => $value ) {
			$data_attr .= 'data-' . $key . '="' . esc_attr( $value ) . '" ';
		}

		$this->headers = [];

		$html = '<table class="fields-repeater ' . $this->css_class() . '" id="' . $this->get_id() . '" ' . $data_attr . '>';

		$html .= '<thead>';
		$html .= '<tr class="row header">';

		$html .= '<th class="handle"></th>';

		foreach ( $this->fields as $sub_field ) {

			// don't print header for hidden field.
			if ( isset( $sub_field->type ) && 'hidden' === $sub_field->type ) {
				continue;
			}

			$description = $sub_field->get_description();

			$html .= '<th class="' . esc_attr( $sub_field->get_raw_name() ) . '">';

			$this->headers[ $sub_field->get_raw_name() ] = $sub_field->get_label();

			$html .= esc_html( $sub_field->get_label() );

			if ( ! empty( $description ) ) {
				$html .= '<small class="description">' . $description . '</small>';
			}

			$html .= '</th>';

		}

		$html .= '<th class="trash"></th>';

		$html .= '</tr>';
		$html .= '</thead>';

		$html .= '<tbody>';

		$html .= $this->row( [], true );

		if ( ! empty( $this->value ) ) {
			foreach ( $this->value as $row_values ) {
				$html .= $this->row( $row_values );
				$this->current_row++;
			}
		}

		$html .= '</tbody>';
		$html .= '</table>';

		$html .= '<a href="#" class="button button-secondary add-new-repeater-field">' . esc_html( $this->add_button_label ) . '</a>';

		return $html;

	}

	/**
	 * Prints repeater row
	 *
	 * @since  5.0.0
	 * @param  array   $values row values.
	 * @param  boolean $model  if this is a hidden model row.
	 * @return string          row HTML
	 */
	public function row( $values = [], $model = false ) {

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

			// don't print useless informations for hidden field.
			if ( isset( $sub_field->type ) && 'hidden' === $sub_field->type ) {
				$html .= $sub_field->field();
			} else {

				$html .= '<td class="subfield ' . esc_attr( $sub_field->get_raw_name() ) . '">';

				if ( isset( $this->headers[ $sub_field->get_raw_name() ] ) ) {
					$html .= '<div class="row-header">' . $this->headers[ $sub_field->get_raw_name() ] . '</div>';
				}

				$html .= '<div class="row-field">';
				$html .= $sub_field->field();
				$html .= '</div>';
				$html .= '</td>';

			}
		}

		$html .= '<td class="trash"></td>';

		$html .= '</tr>';

		return $html;

	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value ) {

		if ( empty( $value ) ) {
			return [];
		}

		$sanitized = [];

		foreach ( $value as $row_id => $row ) {

			$sanitized[ $row_id ] = [];

			foreach ( $this->fields as $sub_field ) {

				$subkey = $sub_field->get_raw_name();

				if ( isset( $row[ $subkey ] ) ) {
					$sanitized_value = $sub_field->sanitize( $row[ $subkey ] );
				} else {
					$sanitized_value = '';
				}

				$sanitized[ $row_id ][ $subkey ] = $sanitized_value;

			}
		}

		return $sanitized;

	}

	/**
	 * Returns the additional field's css classes
	 *
	 * @return string
	 */
	public function css_class() {

		$classes = '';
		if ( $this->sortable ) {
			$classes .= 'fields-repeater-sortable ';
		}

		return $classes . parent::css_class();

	}

}
