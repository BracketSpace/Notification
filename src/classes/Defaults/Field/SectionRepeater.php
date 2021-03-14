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
class SectionRepeater extends Field {

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
	public $field_type = 'section-repeater';

	/**
	 * Carrier object
	 *
	 * @var Sendable
	 */
	protected $carrier;

	/**
	 * Sections
	 *
	 * @var array
	 */
	public $sections = [];

	/**
	 * Section labels
	 *
	 * @var array
	 */
	protected $section_labels = [];

	/**
	 * Field constructor
	 *
	 * @since 5.0.0
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] ) {

		if ( ! isset( $params['sections'] ) ) {
			trigger_error( 'SectionsRepeater requires sections param', E_USER_ERROR );
		}

		if ( ! isset( $params['section_labels'] ) ) {
			trigger_error( 'SectionsRepeater requires section labels param', E_USER_ERROR );
		}

		$this->sections = $params['sections'];

		$this->section_labels = $params['section_labels'];

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

		$html = '<table class="section-repeater fields-repeater ' . $this->css_class() . '" id="' . $this->get_id() . '" ' . $data_attr . '>';

		$html .= '<thead>';
		$html .= '<tr class="row header">';

		$html .= '<th class="handle"></th>';

		foreach ( $this->section_labels as $label ) {

			$html .= '<th class="section-repeater-label">';

			$html .= esc_html( $label );

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

		$html .= '<a href="#" class="button button-secondary add-new-repeater-field add-new-sections-field" @click="addSection">';
		$html .= esc_html( $this->add_button_label );
		$html .= '
			<div class="section-modal"
			v-show="modalOpen"
			>
				<template v-for="(section, index) in sections">
					<span @click="createSection( $event, section )">
						{{ section.name }}
					</span>
				</template>
			</div>
		';
		$html .= '</a>';

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
					<template v-for="( row, key, index ) in rows">
						<sections-row
						:key="key"
						:rows="rows"
						:row="row"
						:type="type"
						:index="index"
						:selected-section="selectedSection"
						:values="values"
						:sub-field-values="subFieldValues"
						:base-fields="baseFields"
						:saved-sections="savedSections"
						:sub-field-values="subFieldValues"
						>
						</sections-row>
					</template>
				</template>
				';

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

		if ( array_keys( $value ) !== range( 0, count( $value ) - 1 ) ) {
			return;
		}

		return $value;

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
