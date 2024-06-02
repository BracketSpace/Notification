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
	 * @param array<mixed> $fields Fields data.
	 * @return  array<mixed>  Modified fields data.
	 * @since 7.0.0
	 */
	public function groupFields($fields)
	{
		$groupedFields = [];

		foreach ($fields as $field) {
			$groupedFields[$field['name']] = $field;
		}

		return $groupedFields;
	}

	/**
	 * Forms field data for sections
	 *
	 * @param array<mixed> $sections Sections data.
	 * @return array<mixed>
	 */
	public function getSectionsFields($sections)
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
	 * @return array<mixed>
	 * @since 7.0.0
	 */
	public function formData()
	{
		$values = $this->getValues($this->postId, $this->carrier, $this->field) ?? [];

		/** @var \BracketSpace\Notification\Repository\Field\SectionRepeater */
		$field = $this->getCarrierFields();

		$populatedSections = $this->getSectionsFields($field->sections);

		return [
			'sections' => $populatedSections,
			'values' => $this->normalizeValues($values),
		];
	}
}
