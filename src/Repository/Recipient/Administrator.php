<?php

/**
 * Administrator recipient
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Recipient;

use BracketSpace\Notification\Repository\Field;

/**
 * Administrator recipient
 */
class Administrator extends BaseRecipient
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
				'slug' => 'administrator',
				'name' => __('Administrator', 'notification'),
				'default_value' => get_option('admin_email'),
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

		return [sanitize_email($value)];
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
				'value' => $this->getDefaultValue(),
				'placeholder' => $this->getDefaultValue(),
				'description' => sprintf(
				// Translators: %s settings URL.
					__('You can edit this email in <a href="%s">General Settings</a>', 'notification'),
					admin_url('options-general.php')
				),
				'disabled' => true,
			]
		);
	}
}
