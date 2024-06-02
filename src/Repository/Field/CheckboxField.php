<?php

/**
 * Checkbox field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Checkbox field class
 */
class CheckboxField extends Field
{
	/**
	 * Checkbox label text
	 * Default: Enable
	 *
	 * @var string
	 */
	protected $checkboxLabel = '';

	/**
	 * Field constructor
	 *
	 * @param array<mixed> $params field configuration parameters.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		$this->checkboxLabel = $params['checkbox_label'] ?? __('Enable', 'notification');

		parent::__construct($params);
	}

	/**
	 * Returns field HTML
	 *
	 * @return string html
	 */
	public function field()
	{
		return sprintf(
			'<label><input type="checkbox" name="%s" id="%s" value="1" %s class="widefat %s" %s> %s</label>',
			esc_attr($this->getName()),
			esc_attr($this->getId()),
			checked($this->getValue(), '1', false),
			esc_attr($this->cssClass()),
			$this->maybeDisable(),
			esc_html($this->checkboxLabel)
		);
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize($value)
	{
		return $value
			? 1
			: 0;
	}
}
