<?php

/**
 * Attachment MIME type merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Media;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;

/**
 * Attachment MIME type merge tag class
 */
class AttachmentMimeType extends StringTag
{
	/**
	 * Merge tag constructor
	 *
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] )
	{

		$this->setTriggerProp($params['property_name'] ?? 'attachment');

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'attachment_mime_type',
				'name' => __('Attachment MIME type', 'notification'),
				'description' => 'image/jpeg',
				'example' => true,
				'resolver' => function ( $trigger ) {
					return $trigger->{ $this->getTriggerProp() }->postMimeType;
				},
				'group' => __('Attachment', 'notification'),
			]
		);

		parent::__construct($args);
	}
}
