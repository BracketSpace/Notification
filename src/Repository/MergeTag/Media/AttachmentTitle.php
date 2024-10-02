<?php

/**
 * Attachment title merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\Media;

use BracketSpace\Notification\Repository\MergeTag\StringTag;

/**
 * Attachment title merge tag class
 */
class AttachmentTitle extends StringTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		$this->setTriggerProp($params['property_name'] ?? 'attachment');

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'attachment_title',
				'name' => __('Attachment title', 'notification'),
				'description' => __('Forest landscape', 'notification'),
				'example' => true,
				'group' => __('Attachment', 'notification'),
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->post_title;
				},
			]
		);

		parent::__construct($args);
	}
}
