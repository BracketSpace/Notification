<?php

/**
 * Recipient with HasReturnField trait for testing
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Repository\Recipient\BaseRecipient;
use BracketSpace\Notification\Repository\Field\InputField;
use BracketSpace\Notification\Traits\HasReturnField;

/**
 * Recipient with HasReturnField trait
 */
class RecipientWithReturnField extends BaseRecipient
{
	use HasReturnField;

	/**
	 * Constructor
	 *
	 * @param string $slug Recipient slug.
	 * @param string $returnField Return field name.
	 */
	public function __construct(string $slug = 'dummy_return_field', string $returnField = 'user_email')
	{
		parent::__construct([
			'slug' => $slug,
			'name' => 'Dummy',
			'default_value' => '',
		]);

		$this->setReturnField($returnField);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $value Raw value.
	 * @return array<mixed>
	 */
	public function parseValue($value = '')
	{
		return [$this->getReturnField() . ':' . $value];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return object
	 */
	public function input()
	{
		return new InputField([
			'label' => 'Recipient',
			'name' => 'recipient',
			'css_class' => 'recipient-value',
		]);
	}
}
