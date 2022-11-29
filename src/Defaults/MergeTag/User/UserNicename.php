<?php

/**
 * User nicename merge tag
 *
 * Requirements:
 * - Trigger property `user_object` or any other passed as
 * `property_name` parameter. Must be an object, preferabely WP_User
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\User;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * User nicename merge tag class
 */
class UserNicename extends StringTag
{
	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] )
	{

		$this->setTriggerProp($params['property_name'] ?? 'user_object');

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'user_nicename',
				'name' => __('User nicename', 'notification'),
				'description' => __('Johhnie', 'notification'),
				'example' => true,
				'group' => __('User', 'notification'),
				'resolver' => function ( $trigger ) {
					return $trigger->{ $this->getTriggerProp() }->userNicename;
				},
			]
		);

		parent::__construct($args);
	}
}
