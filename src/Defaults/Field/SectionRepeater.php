<?php

/**
 * Repeater field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Repeater field class
 */
class SectionRepeater extends Field
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
	public $fieldType = 'section-repeater';

	/**
	 * Carrier object
	 *
	 * @var \BracketSpace\Notification\Interfaces\Sendable
	 */
	protected $carrier;

	/**
	 * Sections
	 *
	 * @var array<mixed>
	 */
	public $sections = [];

	/**
	 * Section labels
	 *
	 * @var array<mixed>
	 */
	protected $sectionLabels = [];

	/**
	 * Field constructor
	 *
	 * @param array<mixed> $params field configuration parameters.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		if (!isset($params['sections'])) {
			trigger_error('SectionsRepeater requires sections param', E_USER_ERROR);
		}

		if (!isset($params['section_labels'])) {
			trigger_error('SectionsRepeater requires section labels param', E_USER_ERROR);
		}

		$this->sections = $params['sections'];

		$this->sectionLabels = $params['section_labels'];

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

		$html = sprintf(
			'<table class="section-repeater fields-repeater %s" id="%s" "%s">',
			esc_attr($this->cssClass()),
			esc_attr($this->getId()),
			$dataAttr
		);

		$html .= '<thead>';
		$html .= '<tr class="row header">';

		$html .= '<th class="handle"></th>';

		foreach ($this->sectionLabels as $label) {
			$html .= '<th class="section-repeater-label">';

			$html .= esc_html($label);

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

		$html .= '<a
		href="#"
		class="button button-secondary add-new-repeater-field add-new-sections-field"
		@click="addSection">';
		$html .= esc_html($this->addButtonLabel);
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
	 * @return string          row HTML
	 * @since  5.0.0
	 */
	public function row()
	{
		return '<template v-if="!repeaterError">
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
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize($value)
	{
		if (empty($value) || !is_array($value)) {
			return [];
		}

		if (array_keys($value) !== range(0, count($value) - 1)) {
			return;
		}

		return $value;
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
