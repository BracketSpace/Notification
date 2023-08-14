<?php

/**
 * Attachment page merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Media;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;

/**
 * Attachment page merge tag class
 */
class AttachmentPage extends UrlTag
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
				'slug' => 'attachment_page_link',
				'name' => __('Attachment page link', 'notification'),
				'description' => __('http://example.com/forest-landscape/', 'notification'),
				'example' => true,
				'group' => __('Attachment', 'notification'),
				'resolver' => function () {
					return get_permalink($this->{$this->getTriggerProp()}->attachment->ID);
				},
			]
		);

		parent::__construct($args);
	}
}
