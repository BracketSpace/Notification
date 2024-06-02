<?php

/**
 * Avatar merge tag class
 *
 * Requirements:
 * - Trigger property `user_object` or any other passed as
 * `property_name` parameter. Must be an object with a `user_email`
 * property, preferably WP_User.
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\User;

use BracketSpace\Notification\Repository\MergeTag\HtmlTag;

/**
 * Avatar merge tag class
 */
class Avatar extends HtmlTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since 6.3.0
	 */
	public function __construct($params = [])
	{
		$this->setTriggerProp($params['property_name'] ?? 'user_object');

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'user_avatar',
				'name' => __('User avatar', 'notification'),
				'description' => __('HTML img tag with avatar', 'notification'),
				'example' => true,
				'group' => __('User', 'notification'),
				'resolver' => function ($trigger) {
					if (isset($trigger->{$this->getTriggerProp()}->user_email)) {
						return get_avatar($trigger->{$this->getTriggerProp()}->user_email);
					}

					return '';
				},
			]
		);

		parent::__construct($args);
	}
}
