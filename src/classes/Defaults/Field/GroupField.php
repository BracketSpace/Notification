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
class GroupField extends Field {

	/**
	 * Fields in group
	 *
	 * @var string
	 */
	protected $fields = [];

	/**
	 * Data attributes
	 *
	 * @var array
	 */
	protected $data_attr = [];

	/**
	 * Repeater field type
	 *
	 * @var string
	 */
	public $field_type = 'group';

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

		// additional data tags for repeater table. key => value array.
		// will be transformed to data-key="value".
		if ( isset( $params['data_attr'] ) ) {
			$this->data_attr = $params['data_attr'];
		}

		if ( isset( $params['carrier'] ) ) {
			$this->carrier = $params['carrier'];
		}

		parent::__construct( $params );

	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field( $values = [], $model = false ) {

		$html = '';

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

}
