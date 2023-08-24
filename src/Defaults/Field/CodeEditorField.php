<?php

/**
 * Code Editor field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Editor field class
 */
class CodeEditorField extends Field
{
	/**
	 * Editor settings
	 *
	 * @see https://codex.wordpress.org/Function_Reference/wp_editor#Arguments
	 * @var string
	 */
	protected $settings = 'text';

	/**
	 * Field constructor
	 *
	 * @param array<mixed> $params field configuration parameters.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		if (isset($params['settings'])) {
			$this->settings = $params['settings'];
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
		$settings = wp_parse_args(
			$this->settings,
			[
				'indentUnit' => 4,
				'tabSize' => 4,
			]
		);

		wp_enqueue_script('code-editor');
		wp_enqueue_style('code-editor');

		$value = is_string($this->getValue()) ? $this->getValue() : '';

		return sprintf(
			'<textarea
				id="%s"
				class="widefat notification-field notification-code-editor-field"
				data-settings="%s"
				rows="10"
				name="%s"
			>%s</textarea>',
			esc_attr($this->getId()),
			esc_attr(wp_json_encode($settings)),
			esc_attr($this->getName()),
			esc_textarea($value)
		);
	}

	/**
	 * The code is not sanitized
	 *
	 * @param mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize($value)
	{
		return $value;
	}
}
