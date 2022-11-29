<?php

/**
 * Checkbox field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Field;

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
	 * @since 5.0.0
	 * @param array $params field configuration parameters.
	 */
	public function __construct( $params = [] )
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
		return '<label><input type="checkbox" name="' . esc_attr($this->getName()) . '" id="' . esc_attr($this->getId()) . '" value="1" ' . checked($this->getValue(), '1', false) . ' class="widefat ' . esc_attr($this->cssClass()) . '" ' . $this->maybeDisable() . '> ' . esc_html($this->checkboxLabel) . '</label>';
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param  mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize( $value )
	{
		return $value ? 1 : 0;
	}
}
