<?php

/**
 * Has Name Trait.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Traits;

use BracketSpace\Notification\ErrorHandler;

/**
 * HasName trait
 */
trait HasReturnField
{
	/**
	 * Return field name.
	 *
	 * @var ?string
	 */
	protected $returnField;

	/**
	 * Gets the default return field name.
	 *
	 * @return string
	 */
	public function getDefaultReturnField()
	{
		return 'user_email';
	}

	/**
	 * Gets the return field name.
	 *
	 * @return string
	 */
	public function getReturnField()
	{
		return $this->returnField ?? $this->getDefaultReturnField();
	}

	/**
	 * Sets return field name.
	 *
	 * @param string $returnField Return field name.
	 * @return $this
	 */
	public function setReturnField(string $returnField)
	{
		$availableReturnFields = ['ID', 'user_email'];

		if (!in_array($returnField, $availableReturnFields, true)) {
			ErrorHandler::error(sprintf('Recipient return field "%s" is not supported.', $returnField));

			$returnField = $this->getDefaultReturnField();
		}

		$this->returnField = $returnField;

		return $this;
	}
}
