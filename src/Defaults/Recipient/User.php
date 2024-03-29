<?php

/**
 * User recipient
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Recipient;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;
use BracketSpace\Notification\Queries\UserQueries;

/**
 * User recipient
 */
class User extends Abstracts\Recipient
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
				'slug' => 'user',
				'name' => __('User', 'notification'),
				'default_value' => get_current_user_id(),
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

		$user = get_userdata((int)$value);

		if ($user) {
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			return [$user->user_email];
		}

		return [];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return object
	 */
	public function input()
	{
		$opts = [];

		foreach (UserQueries::all() as $user) {
			$opts[$user['ID']] = esc_html($user['display_name']) . ' (' . $user['user_email'] . ')';
		}

		return new Field\SelectField(
			[
				'label' => __('Recipient', 'notification'), // don't edit this!
				'name' => 'recipient',                       // don't edit this!
				'css_class' => 'recipient-value',                 // don't edit this!
				'value' => $this->getDefaultValue(),
				'pretty' => true,
				'options' => $opts,
			]
		);
	}
}
