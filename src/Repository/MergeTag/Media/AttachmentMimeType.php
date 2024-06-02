<?php

/**
 * Attachment MIME type merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\Media;

use BracketSpace\Notification\Repository\MergeTag\StringTag;

/**
 * Attachment MIME type merge tag class
 */
class AttachmentMimeType extends StringTag
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
				'slug' => 'attachment_mime_type',
				'name' => __('Attachment MIME type', 'notification'),
				'description' => 'image/jpeg',
				'example' => true,
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->post_mime_type;
				},
				'group' => __('Attachment', 'notification'),
			]
		);

		parent::__construct($args);
	}
}
