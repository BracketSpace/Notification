<?php

/**
 * User ID recipient
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Recipient;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Repository\Field;

/**
 * User ID recipient
 */
class UserID extends Abstracts\Recipient
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
				'slug' => 'user_id',
				'name' => __('User ID', 'notification'),
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
			return [];
		}

		$userIds = array_map('trim', explode(',', $value));
		$users = get_users(
			[
				'include' => $userIds,
				'fields' => ['user_email'],
			]
		);

		return wp_list_pluck($users, 'user_email');
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
				'placeholder' => __('123 or {user_ID}', 'notification'),
				'description' => __('You can use any valid email merge tag.', 'notification'),
				'resolvable' => true,
			]
		);
	}
}
