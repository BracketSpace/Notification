<?php

/**
 * Select field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Select field class
 */
class SelectField extends Field
{
	/**
	 * Field options
	 * value => label array
	 *
	 * @var array<mixed>
	 */
	protected $options = [];

	/**
	 * Class for pretty select
	 * Will be used by JS to print Selectize input
	 *
	 * @var string
	 */
	protected $pretty = '';

	/**
	 * Field constructor
	 *
	 * @param array<mixed> $params field configuration parameters.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		if (isset($params['options'])) {
			$this->options = $params['options'];
		}

		if (isset($params['pretty']) && $params['pretty']) {
			$this->pretty = 'notification-pretty-select';
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
		$cssClasses = $this->pretty . ' ' . $this->cssClass();

		$html = '<select name="' . esc_attr($this->getName()) . '" id="' . esc_attr(
			$this->getId()
		) . '" class="' . $cssClasses . '" ' . $this->maybeDisable() . '>';

		foreach ($this->options as $optionValue => $optionLabel) {
			$html .= '<option value="' . esc_attr($optionValue) . '" ' . selected(
				$this->getValue(),
				$optionValue,
				false
			) . '>' . esc_html($optionLabel) . '</option>';
		}

		$html .= '</select>';

		return $html;
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize($value)
	{
		return sanitize_text_field($value);
	}
}
