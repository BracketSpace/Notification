<?php

/**
 * Role recipient
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Recipient;

use BracketSpace\Notification\Database\Queries\UserQueries;
use BracketSpace\Notification\Repository\Field;
use BracketSpace\Notification\Traits;

/**
 * Role recipient
 */
class Role extends BaseRecipient
{
	use Traits\HasReturnField;

	/**
	 * Recipient constructor
	 *
	 * @since 5.0.0
	 * @param array<mixed> $params recipient configuration params.
	 */
	public function __construct($params = [])
	{
		$this->setReturnField(
			is_string($params['return_field'] ?? null)
				? $params['return_field']
				: $this->getDefaultReturnField()
		);

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

		return wp_list_pluck(UserQueries::withRole($value), $this->getReturnField());
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
