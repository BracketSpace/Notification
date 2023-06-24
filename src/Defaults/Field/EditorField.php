<?php

/**
 * Editor field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Field;

use BracketSpace\Notification\Abstracts\Field;

/**
 * Editor field class
 */
class EditorField extends Field
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
				'textarea_name' => $this->getName(),
				'textarea_rows' => 20,
				'editor_class' => $this->cssClass(),
			]
		);

		ob_start();

		wp_editor(
			$this->getValue(),
			$this->getId(),
			$settings
		);

		return ob_get_clean();
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize($value)
	{

		/**
		 * Fixes WPLinkPreview TinyMCE component which adds the https:// prefix to invalid URL.
		 */
		return str_replace(
			['https://{', 'http://{'],
			'{',
			$value
		);
	}
}
