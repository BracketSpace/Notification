<?php

/**
 * Role recipient
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Recipient;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;
use BracketSpace\Notification\Queries\UserQueries;

/**
 * Role recipient
 */
class Role extends Abstracts\Recipient
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
				'slug' => 'role',
				'name' => __('Role', 'notification'),
				'default_value' => 'administrator',
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

		$emails = [];

		foreach (UserQueries::withRole($value) as $user) {
			$emails[] = $user['user_email'];
		}

		return $emails;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return object
	 */
	public function input()
	{
		if (!function_exists('get_editable_roles')) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		$roles = get_editable_roles();
		$opts = [];

		foreach ($roles as $roleSlug => $role) {
			$numUsers = count(UserQueries::withRole($roleSlug));

			$label = translate_user_role($role['name']) . ' (' . sprintf(
				// Translators: %s numer of users.
				_n(
					'%s user',
					'%s users',
					$numUsers,
					'notification'
				),
				$numUsers
			) . ')';

			$opts[$roleSlug] = esc_html($label);
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
