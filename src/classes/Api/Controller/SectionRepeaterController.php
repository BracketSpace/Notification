<?php
/**
 * Repeater Handler class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Api\Controller;

/**
 * RepeaterHandler class
 *
 * @action
 */
class SectionRepeaterController extends RepeaterController {

	/**
	 * Forms field data for sections
	 *
	 * @param array $sections Sections data.
	 * @return array
	 */
	public function get_sections_fields( $sections ) {

		$section_fields = [];

		foreach ( $sections as $section => $value ) {

			$section_fields[ $section ]['name']   = ucfirst( $section );
			$section_fields[ $section ]['fields'] = $this->form_field_data( $value['fields'] );

			foreach ( $section_fields[ $section ]['fields'] as &$field ) {

				if ( $field['sections'] ) {
					$sections = [];

					foreach ( $field['sections'] as $section ) {
						$section_field           = [];
						$section_field['name']   = $section['name'];
						$section_field['fields'] = $this->form_field_data( $section['fields'] );
						$sections                = array_merge( $sections, $section_field );

					}

					$field = $sections;

				}
			}
		}

		return $section_fields;
	}

	/**
	 * Forms response data
	 *
	 * @since [Next]
	 * @return array
	 */
	public function form_data() {
		$values             = $this->get_values( $this->post_id, $this->carrier, $this->field ) ?? [];
		$populated_sections = $this->get_sections_fields( $this->get_carrier_fields()->sections );

		$data = [
			'sections' => $populated_sections,
			'values'   => $this->normalize_values( $values ),
		];

		return $data;
	}
}
