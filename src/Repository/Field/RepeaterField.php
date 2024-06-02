<?php

/**
 * Repeater field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Repeater field class
 */
class RepeaterField extends Field
{
	/**
	 * Current repeater row
	 *
	 * @var int
	 */
	protected $currentRow = 0;

	/**
	 * Fields to repeat
	 *
	 * @var array<\BracketSpace\Notification\Abstracts\Field>
	 */
	public $fields = [];

	/**
	 * Add new button label
	 *
	 * @var string
	 */
	protected $addButtonLabel = '';

	/**
	 * Data attributes
	 *
	 * @var array<mixed>
	 */
	protected $dataAttr = [];

	/**
	 * Row headers
	 *
	 * @var array<mixed>
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
	public $fieldType = 'repeater';

	/**
	 * Carrier object
	 *
	 * @var \BracketSpace\Notification\Interfaces\Sendable
	 */
	protected $carrier;

	/**
	 * If the global description in the header should be printed
	 *
	 * @var bool
	 */
	public $printHeaderDescription = true;

	/**
	 * Field constructor
	 *
	 * @param array<mixed> $params field configuration parameters.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		if (isset($params['fields'])) {
			$this->fields = $params['fields'];
		}

		$this->addButtonLabel = $params['add_button_label'] ?? __('Add new', 'notification');

		// additional data tags for repeater table. key => value array.
		// will be transformed to data-key="value".
		if (isset($params['data_attr'])) {
			$this->dataAttr = $params['data_attr'];
		}

		if (isset($params['sortable']) && !$params['sortable']) {
			$this->sortable = false;
		}

		if (isset($params['carrier'])) {
			$this->carrier = $params['carrier'];
		}

		parent::__construct($params);
	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field()
	{
		$dataAttr = '';
		foreach ($this->dataAttr as $key => $value) {
			$dataAttr .= 'data-' . $key . '="' . esc_attr($value) . '" ';
		}

		$this->headers = [];

		$html =
			'<table class="fields-repeater ' . $this->cssClass() . '" id="' . $this->getId() . '" ' . $dataAttr . '>';

		$html .= '<thead>';
		$html .= '<tr class="row header">';

		$html .= '<th class="handle"></th>';

		foreach ($this->fields as $subField) {
			// don't print header for hidden field.
			if (isset($subField->type) && $subField->type === 'hidden') {
				continue;
			}

			$html .= '<th class="' . esc_attr($subField->getRawName()) . '">';

			$this->headers[$subField->getRawName()] = $subField->getLabel();

			$html .= esc_html($subField->getLabel());

			$description = $subField->getDescription();

			if ($this->printHeaderDescription && !empty($description)) {
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

		$html .= sprintf(
			'<template v-if="repeaterError">
				<div class="repeater-error">
					%s
				</div>
			</template>',
			$this->restApiError()
		);

		$html .= '<a href="#" class="button button-secondary add-new-repeater-field" @click="addField">' . esc_html(
			$this->addButtonLabel
		) . '</a>';

		return $html;
	}

	/**
	 * Prints repeater row
	 *
	 * @return string          row HTML
	 * @since  5.0.0
	 */
	public function row()
	{
		return '<template v-if="!repeaterError">
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
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize($value)
	{
		if (empty($value)) {
			return [];
		}

		$sanitized = [];

		foreach ($value as $rowId => $row) {
			$sanitized[$rowId] = [];

			foreach ($this->fields as $subField) {
				$subkey = $subField->getRawName();

				$sanitizedValue = isset($row[$subkey])
					? $subField->sanitize($row[$subkey])
					: '';

				$sanitized[$rowId][$subkey] = $sanitizedValue;
			}
		}

		return $sanitized;
	}

	/**
	 * Returns the additional field's css classes
	 *
	 * @return string
	 */
	public function cssClass()
	{
		$classes = '';
		if ($this->sortable) {
			$classes .= 'fields-repeater-sortable ';
		}

		return $classes . parent::cssClass();
	}
}
