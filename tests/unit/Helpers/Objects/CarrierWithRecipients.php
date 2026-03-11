<?php

/**
 * Carrier with recipients field for testing
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Tests\Helpers\Objects;

use BracketSpace\Notification\Repository\Carrier\BaseCarrier;
use BracketSpace\Notification\Interfaces\Triggerable;

/**
 * Carrier with recipients field
 */
class CarrierWithRecipients extends BaseCarrier
{
	/**
	 * Constructor
	 *
	 * @param string $slug Carrier slug.
	 */
	public function __construct(string $slug = 'test_carrier')
	{
		parent::__construct($slug, 'Test Carrier');
	}

	/**
	 * {@inheritdoc}
	 */
	public function formFields()
	{
		$this->addRecipientsField();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param Triggerable $trigger Trigger object.
	 */
	public function send(Triggerable $trigger)
	{
	}

	/**
	 * Exposes protected recipientsResolvedData for testing
	 *
	 * @param array<mixed> $data Resolved data.
	 */
	public function setRecipientsResolvedData(array $data)
	{
		$this->recipientsResolvedData = $data;
	}
}
