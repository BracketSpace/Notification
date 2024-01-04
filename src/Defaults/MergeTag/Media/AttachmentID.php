<?php

/**
 * Attachment ID merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Media;

use BracketSpace\Notification\Defaults\MergeTag\IntegerTag;

/**
 * Attachment ID merge tag class
 */
class AttachmentID extends IntegerTag
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
				'slug' => 'attachment_ID',
				'name' => __('Attachment ID', 'notification'),
				'description' => '35',
				'example' => true,
				'group' => __('Attachment', 'notification'),
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->ID;
				},
			]
		);

		parent::__construct($args);
	}
}
