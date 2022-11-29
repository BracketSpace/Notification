<?php

/**
 * Attachment direct URL merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Media;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;

/**
 * Attachment direct URL merge tag class
 */
class AttachmentDirectUrl extends UrlTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array $params merge tag configuration params.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{

		$this->setTriggerProp($params['property_name'] ?? 'attachment');

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'attachment_direct_url',
				'name' => __(
					'Attachment direct URL',
					'notification'
				),
				'description' => __(
					'http://example.com/wp-content/uploads/2018/02/forest-landscape.jpg',
					'notification'
				),
				'example' => true,
				'group' => __(
					'Attachment',
					'notification'
				),
				'resolver' => function () {
					return wp_get_attachment_url($this->trigger->{$this->getTriggerProp()}->ID);
				},
			]
		);

		parent::__construct($args);

		$this->setGroup(
			__(
				'Attachment',
				'notification'
			)
		);
	}
}
