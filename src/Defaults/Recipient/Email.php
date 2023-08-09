<?php

/**
 * Email recipient
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Recipient;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;

/**
 * Email recipient
 */
class Email extends Abstracts\Recipient
{
	/**
	 * Recipient constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct()
	{
		parent::__construct(
			[
				'slug' => 'email',
				'name' => __('Email / Merge tag', 'notification'),
				'default_value' => '',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $value raw value saved by the user.
	 * @return array<mixed>         array of resolved values
	 */
	public function parseValue($value = '')
	{

		if (empty($value)) {
			$value = $this->getDefaultValue();
		}

		/**
		 * Include 'filter-id:your-favorite-id' in value to specify a filter id.
		 *
		 * Defaults to 'default' (ie. filter 'notification/recipient/email/default'):
		 */
		$filterId = 'default';
		$pattern = '/\bfilter-id:([\w-]*)/';

		if (
			preg_match(
				$pattern,
				$value,
				$matches
			)
		) {
			$filterId = $matches[1];
			$value = preg_replace(
				$pattern,
				'',
				$value
			);
			$value = is_string($value)
				? trim($value)
				: '';
		}

		$value = apply_filters('notification/recipient/email/' . $filterId, $value);

		$parsedEmails = [];
		$emails = is_array($value)
			? $value
			: preg_split(
				'/[;|,]/',
				$value
			);

		if (!$emails) {
			return [];
		}

		foreach ($emails as $email) {
			$parsedEmails[] = sanitize_email($email);
		}

		return $parsedEmails;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return object
	 */
	public function input()
	{

		return new Field\InputField(
			[
				'label' => __('Recipient', 'notification'), // don't edit this!
				'name' => 'recipient',                       // don't edit this!
				'css_class' => 'recipient-value',                 // don't edit this!
				'placeholder' => __('email@domain.com or {email}', 'notification'),
				'description' => __('You can use any valid email merge tag.', 'notification'),
				'resolvable' => true,
			]
		);
	}
}
