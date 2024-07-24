<?php

/**
 * Recipient abstract class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Recipient;

use BracketSpace\Notification\Dependencies\Micropackage\Casegnostic\Casegnostic;
use BracketSpace\Notification\Interfaces;
use BracketSpace\Notification\Traits;

/**
 * Recipient abstract class
 */
abstract class BaseRecipient implements Interfaces\Receivable
{
	use Traits\ClassUtils;
	use Traits\HasName;
	use Traits\HasSlug;
	use Casegnostic;

	/**
	 * List of available return fields.
	 */
	protected const AVAILABLE_RETURN_FIELDS = ['ID', 'user_email'];

	/**
	 * Return field key name.
	 *
	 * @var string
	 */
	protected $returnField = 'user_email';

	/**
	 * Recipient input default value
	 *
	 * @var string
	 */
	protected $defaultValue;

	/**
	 * Recipient constructor
	 *
	 * @param array<mixed> $params recipient configuration params.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		if (!empty($params['slug'])) {
			$this->setSlug($params['slug']);
		}

		if (!empty($params['name'])) {
			$this->setName($params['name']);
		}

		if (is_string($params['return_field'] ?? null)) {
			$returnField = $params['return_field'];

			if (!in_array($returnField, self::AVAILABLE_RETURN_FIELDS, true)) {
				trigger_error(sprintf('Recipient return field "%s" is not supported.', $returnField), E_USER_ERROR);
			}

			$this->returnField = $returnField;
		}

		if (!isset($params['default_value'])) {
			trigger_error('Recipient requires default_value', E_USER_ERROR);
		}

		$this->defaultValue = $params['default_value'];
	}

	/**
	 * Parses saved value something understood by the Carrier
	 *
	 * @param string $value raw value saved by the user.
	 * @return array<mixed> array of resolved values
	 */
	public function parseValue($value = '')
	{
		return [true];
	}

	/**
	 * Returns input object
	 *
	 * @return \BracketSpace\Notification\Interfaces\Fillable
	 */
	abstract public function input();

	/**
	 * Gets default value
	 *
	 * @return string
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}
}
