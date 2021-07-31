<?php
/**
 * Repeater field class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;
use BracketSpace\Notification\Interfaces\Sendable;

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
	 * @var Field[]
	 */
	public $fields = [];

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
	 * @var bool
	 */
	protected $sortable = true;

	/**
	 * Repeater field type
	 *
	 * @var string
	 */
	public $field_type = 'repeater';

	/**
	 * Carrier object
	 *
	 * @var Sendable
	 */
	protected $carrier;

	/**
	 * If the global description in the header should be printed
	 *
	 * @var bool
	 */
	public $print_header_description = true;

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

			$html .= '<th class="' . esc_attr( $sub_field->get_raw_name() ) . '">';

			$this->headers[ $sub_field->get_raw_name() ] = $sub_field->get_label();

			$html .= esc_html( $sub_field->get_label() );

			$description = $sub_field->get_description();

			if ( $this->print_header_description && ! empty( $description ) ) {
				$html .= '<small class="description">' . $description . '</small>';
			}

			$html .= '</th>';

		}

		$html .= '<th class="trash"></th>';

		$html .= '</tr>';
		$html .= '</thead>';

		$html .= '<tbody>';

		$html .= $this->row();

		$html .= '</tbody>';
		$html .= '</table>';

		$html .= '<template v-if="repeaterError">
					<div class="repeater-error">'
					. $this->rest_api_error() .
					'</div>
				  </template>';

		$html .= '<a href="#" class="button button-secondary add-new-repeater-field" @click="addField">' . esc_html( $this->add_button_label ) . '</a>';

		return $html;

	}

	/**
	 * Prints repeater row
	 *
	 * @since  5.0.0
	 * @return string          row HTML
	 */
	public function row() {
		$html = '<template v-if="!repeaterError">
					<template v-for="( field, key ) in fields">
						<repeater-row
						:field="field"
						:fields="fields"
						:type="type"
						:key-index="key"
						:nested-fields="nestedFields"
						:nested-values="nestedValues"
						:nested-model="nestedModel"
						:nested-row-count="nestedRowCount"
						>
						</repeater-row>
					</template>
				</template>';

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
