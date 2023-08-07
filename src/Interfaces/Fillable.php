<?php

/**
 * Fillable interface class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Interfaces;

interface Fillable
{
	/**
	 * Gets field value
	 *
	 * @return mixed
	 */
	public function getValue();

	/**
	 * Sets field value
	 *
	 * @param mixed $value value from DB.
	 * @return void
	 */
	public function setValue($value);

	/**
	 * Gets field section name
	 *
	 * @return string
	 */
	public function getSection();

	/**
	 * Sets field section name
	 *
	 * @param string $value assigned value
	 * @return void
	 */
	public function setSection($value);

	/**
	 * Gets field name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Gets raw field name
	 *
	 * @return string
	 */
	public function getRawName();

	/**
	 * Gets field label
	 *
	 * @return string
	 */
	public function getLabel();

	/**
	 * Gets field ID
	 *
	 * @return string
	 */
	public function getId();

	/**
	 * Gets field description
	 *
	 * @return string
	 */
	public function getDescription();

	/**
	 * Returns the additional field's css classes
	 *
	 * @return string
	 */
	public function cssClass();

	/**
	 * Checks if field should be resolved with merge tags
	 *
	 * @return bool
	 */
	public function isResolvable();

	/**
	 * Sanitizes the value sent by user
	 *
	 * @param mixed $value value to sanitize.
	 * @return mixed        sanitized value
	 */
	public function sanitize($value);
}
