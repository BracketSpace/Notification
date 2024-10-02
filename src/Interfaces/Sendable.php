<?php

/**
 * Sendable interface class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Interfaces;

/**
 * Sendable interface
 */
interface Sendable extends Nameable
{
	/**
	 * Activates the Carrier
	 *
	 * @return $this
	 */
	public function activate();

	/**
	 * Sends the carrier
	 *
	 * @param \BracketSpace\Notification\Interfaces\Triggerable $trigger trigger object.
	 * @return void
	 */
	public function send(Triggerable $trigger);

	/**
	 * Generates an unique hash for carrier instance
	 *
	 * @return string
	 */
	public function hash();

	/**
	 * Gets form fields array
	 *
	 * @return array<\BracketSpace\Notification\Interfaces\Fillable> fields
	 */
	public function getFormFields();

	/**
	 * Checks if Carrier is enabled
	 *
	 * @return bool
	 */
	public function isEnabled();

	/**
	 * Checks if Carrier is active
	 *
	 * @return bool
	 */
	public function isActive();

	/**
	 * Sets data from array
	 *
	 * @param array<string,mixed> $data Data with keys matched with Field names.
	 * @return $this
	 */
	public function setData($data);

	/**
	 * Gets data
	 *
	 * @return array<string,mixed>
	 */
	public function getData();

	/**
	 * Enables the Carrier
	 *
	 * @return $this
	 */
	public function enable();

	/**
	 * Disables the Carrier
	 *
	 * @return $this
	 */
	public function disable();

	/**
	 * Gets form fields array
	 *
	 * @param string $fieldName Field name.
	 * @return mixed              Field object or null.
	 */
	public function getFormField($fieldName);

	/**
	 * Gets the saved recipients
	 *
	 * @return array<mixed>
	 */
	public function getRecipients();

	/**
	 * Gets field value
	 *
	 * @param string $fieldSlug field slug.
	 * @return mixed            value or null if field not available
	 */
	public function getFieldValue($fieldSlug);

	/**
	 * Gets the recipients field
	 * Calls the field closure.
	 *
	 * @return \BracketSpace\Notification\Repository\Field\RecipientsField|null
	 * @since  8.0.0
	 */
	public function getRecipientsField();

	/**
	 * Checks if the recipients field was added
	 *
	 * @return bool
	 * @since  8.0.0
	 */
	public function hasRecipientsField();
}
