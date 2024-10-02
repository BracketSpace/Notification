<?php

/**
 * Input field class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Field;

/**
 * Input field class
 */
class InputField extends BaseField
{
	/**
	 * Field type
	 * possible values are valid HTML5 types except file or checkbox
	 *
	 * @var string
	 */
	public $type = 'text';

	/**
	 * Field placeholder
	 *
	 * @var string
	 */
	protected $placeholder = '';

	/**
	 * Field attributes
	 *
	 * @var string
	 */
	protected $atts = '';

	/**
	 * Allow for line breaks while sanitizing
	 *
	 * @since 6.3.1
	 * @var bool
	 */
	protected $allowLinebreaks = false;

	/**
	 * Field constructor
	 *
	 * @param array<mixed> $params field configuration parameters.
	 * @since 6.3.1 Allow for whitespace characters.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		if (isset($params['type'])) {
			$this->type = $params['type'];
		}

		if (isset($params['placeholder'])) {
			$this->placeholder = $params['placeholder'];
		}

		if (isset($params['atts'])) {
			$this->atts = $params['atts'];
		}

		if (isset($params['allow_linebreaks'])) {
			$this->allowLinebreaks = $params['allow_linebreaks'];
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
		return sprintf(
			'<input type="%s" name="%s" id="%s" value="%s" placeholder="%s" class="widefat %s" %s %s>',
			esc_attr($this->type),
			esc_attr($this->getName()),
			esc_attr($this->getId()),
			esc_attr($this->getValue()),
			esc_attr($this->placeholder),
			esc_attr($this->cssClass()),
			$this->maybeDisable(),
			esc_attr($this->atts)
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
		// Remove script and style tags.
		$value = preg_replace(
			'@<(script|style)[^>]*?>.*?</\\1>@si',
			'',
			(string)$value
		);

		// Remove line breaks.
		if ($this->allowLinebreaks !== true) {
			$value = preg_replace(
				'/[\r\n\t ]+/',
				' ',
				(string)$value
			);
		}

		// Remove whitespace.
		$value = trim((string)$value);

		return $value;
	}
}
