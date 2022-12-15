<?php

/**
 * Textarea field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Textarea field class
 */
class TextareaField extends Field
{

	/**
	 * Field placeholder
	 *
	 * @var string
	 */
	protected $placeholder = '';

	/**
	 * Textarea rows
	 *
	 * @var int
	 */
	protected $rows = 10;

	/**
	 * If unfiltered value is allowed
	 *
	 * @var bool
	 */
	protected $allowedUnfiltered = false;

	/**
	 * Field constructor
	 *
	 * @param array<mixed> $params field configuration parameters.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{

		if (isset($params['placeholder'])) {
			$this->placeholder = $params['placeholder'];
		}

		if (isset($params['rows'])) {
			$this->rows = $params['rows'];
		}

		if (isset($params['allowed_unfiltered']) && $params['allowed_unfiltered']) {
			$this->allowedUnfiltered = true;
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
		return '<textarea name="' . esc_attr($this->getName()) . '" rows="' . esc_attr(
			(string)$this->rows
		) . '" id="' . esc_attr($this->getId()) . '" placeholder="' . esc_attr(
			$this->placeholder
		) . '" class="widefat ' . esc_attr($this->cssClass()) . '" ' . $this->maybeDisable(
		) . '>' . $this->getValue() . '</textarea>';
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize($value)
	{
		return ($this->allowedUnfiltered)
			? $value
			: sanitize_textarea_field($value);
	}
}
