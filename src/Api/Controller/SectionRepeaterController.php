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
	 * Group fields in associative array
	 *
	 * @since 7.0.0
	 * @param array $fields Fields data.
	 * @return  array  Modified fields data.
	 */
	public function group_fields( $fields ) {

		$groupped_fields = [];

		foreach ( $fields as $field ) {
			$groupped_fields[ $field['name'] ] = $field;
		}

		return $groupped_fields;
	}

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
			$base_fields                          = $this->form_field_data( $value['fields'] );
			$groupped_fields                      = $this->group_fields( $base_fields );
			$section_fields[ $section ]['fields'] = $groupped_fields;

			foreach ( $section_fields[ $section ]['fields'] as &$field ) {

				if ( $field['sections'] ) {
					$sections = [];

					foreach ( $field['sections'] as $section ) {

						$section_field             = [];
						$section_field['name']     = $section['name'];
						$section_field['multiple'] = isset( $section['multiple_section'] ) ? $section['multiple_section'] : false;
						$section_field['special']  = isset( $section['special_section'] ) ? $section['special_section'] : false;
						$base_sub_fields           = $this->form_field_data( $section['fields'] );
						$groupped_sub_fields       = $this->group_fields( $base_sub_fields );
						$section_field['fields']   = $groupped_sub_fields;
						$sections                  = array_merge( $sections, $section_field );

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
	 * @since 7.0.0
	 * @return array
	 */
	public function form_data() {
		$values = $this->get_values( $this->post_id, $this->carrier, $this->field ) ?? [];

		/** @var \BracketSpace\Notification\Defaults\Field\SectionRepeater */
		$field = $this->get_carrier_fields();

		$populated_sections = $this->get_sections_fields( $field->sections );

		$data = [
			'sections' => $populated_sections,
			'values'   => $this->normalize_values( $values ),
		];

		return $data;
	}
}
