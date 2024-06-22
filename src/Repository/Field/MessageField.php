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
class MessageField extends BaseField
{
	/**
	 * Message
	 *
	 * @var string
	 */
	protected $message = '';

	/**
	 * Field type
	 *
	 * @var string
	 */
	protected $type = '';

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
	 * Field constructor
	 *
	 * @param array<mixed> $params field configuration parameters.
	 * @since 6.3.1 Allow for whitespace characters.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		if (!isset($params['message'])) {
			trigger_error('MessageField requires message param', E_USER_ERROR);
		}

		$this->message = $params['message'];

		if (isset($params['type'])) {
			$this->type = $params['type'];
		}

		if (isset($params['name'])) {
			$this->name = $params['name'];
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
		$value = preg_replace(
			'@<(script|style)[^>]*?>.*?</\\1>@si',
			'',
			$value
		); // Remove script and style tags.

		$value = trim($value); // Remove whitespace.
		return $value;
	}
}
