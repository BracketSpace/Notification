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
	public function groupFields( $fields )
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
	public function getSectionsFields( $sections )
	{

		$sectionFields = [];

		foreach ($sections as $section => $value) {
			$sectionFields[$section]['name'] = ucfirst($section);
			$baseFields = $this->formFieldData($value['fields']);
			$grouppedFields = $this->groupFields($baseFields);
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
					$baseSubFields = $this->formFieldData($section['fields']);
					$grouppedSubFields = $this->groupFields($baseSubFields);
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
	public function formData()
	{
		$values = $this->getValues($this->postId, $this->carrier, $this->field) ?? [];

		/** @var \BracketSpace\Notification\Defaults\Field\SectionRepeater */
		$field = $this->getCarrierFields();

		$populatedSections = $this->getSectionsFields($field->sections);

		return [
			'sections' => $populatedSections,
			'values' => $this->normalizeValues($values),
		];
	}
}
