<?php

/**
 * Repeater Handler class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Api\Controller;

/**
 * RepeaterHandler class
 *
 * @action
 */
class SectionRepeaterController extends RepeaterController
{

	/**
	 * Group fields in associative array
	 *
	 * @since 7.0.0
	 * @param array $fields Fields data.
	 * @return  array  Modified fields data.
	 */
	public function group_fields( $fields )
	{

		$grouppedFields = [];

		foreach ($fields as $field) {
			$grouppedFields[$field['name']] = $field;
		}

		return $grouppedFields;
	}

	/**
	 * Forms field data for sections
	 *
	 * @param array $sections Sections data.
	 * @return array
	 */
	public function get_sections_fields( $sections )
	{

		$sectionFields = [];

		foreach ($sections as $section => $value) {
			$sectionFields[$section]['name'] = ucfirst($section);
			$baseFields = $this->form_field_data($value['fields']);
			$grouppedFields = $this->group_fields($baseFields);
			$sectionFields[$section]['fields'] = $grouppedFields;

			foreach ($sectionFields[$section]['fields'] as &$field) {
				if (!$field['sections']) {
					continue;
				}

				$sections = [];

				foreach ($field['sections'] as $section) {
					$sectionField = [];
					$sectionField['name'] = $section['name'];
					$sectionField['multiple'] = $section['multiple_section'] ?? false;
					$sectionField['special'] = $section['special_section'] ?? false;
					$baseSubFields = $this->form_field_data($section['fields']);
					$grouppedSubFields = $this->group_fields($baseSubFields);
					$sectionField['fields'] = $grouppedSubFields;
					$sections = array_merge($sections, $sectionField);
				}

				$field = $sections;
			}
		}

		return $sectionFields;
	}

	/**
	 * Forms response data
	 *
	 * @since 7.0.0
	 * @return array
	 */
	public function form_data()
	{
		$values = $this->get_values($this->post_id, $this->carrier, $this->field) ?? [];

		/** @var \BracketSpace\Notification\Defaults\Field\SectionRepeater */
		$field = $this->get_carrier_fields();

		$populatedSections = $this->get_sections_fields($field->sections);

		return [
			'sections' => $populatedSections,
			'values' => $this->normalize_values($values),
		];
	}
}
