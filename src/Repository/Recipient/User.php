<?php

/**
 * User recipient
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Recipient;

use BracketSpace\Notification\Repository\Field;
use BracketSpace\Notification\Database\Queries\UserQueries;
use BracketSpace\Notification\Traits;

/**
 * User recipient
 */
class User extends BaseRecipient
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
			return [$user->{$this->getReturnField()}];
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
