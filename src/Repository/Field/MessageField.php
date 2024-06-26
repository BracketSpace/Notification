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
		return wp_kses_post(is_callable($this->message) ? $this->message() : $this->message);
	}

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param mixed $value value to sanitize.
	 * @return null
	 */
	public function sanitize($value)
	{
		return null;
	}
}
