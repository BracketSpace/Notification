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
		$value = is_string($this->getValue()) ? $this->getValue() : '';

		return sprintf(
			'<textarea name="%s" rows="%s" id="%s" placeholder="%s" class="widefat %s" %s>%s</textarea>',
			esc_attr($this->getName()),
			esc_attr((string)$this->rows),
			esc_attr($this->getId()),
			esc_attr($this->placeholder),
			esc_attr($this->cssClass()),
			$this->maybeDisable(),
			esc_textarea($value)
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
		return ($this->allowedUnfiltered) ? $value  : sanitize_textarea_field($value);
	}
}
