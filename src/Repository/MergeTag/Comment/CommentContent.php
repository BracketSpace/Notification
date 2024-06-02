<?php

/**
 * Comment content merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\Comment;

use BracketSpace\Notification\Repository\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment content merge tag class
 */
class CommentContent extends StringTag
{
	/**
	 * Trigger property to get the comment data from
	 *
	 * @var string
	 */
	protected $commentType = 'comment';

	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{
		if (isset($params['comment_type']) && !empty($params['comment_type'])) {
			$this->commentType = $params['comment_type'];
		}

		$this->setTriggerProp($params['property_name'] ?? $this->commentType);

		$commentTypeName = WpObjectHelper::getCommentTypeName($this->commentType);

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'comment_content',
				// Translators: Comment type name.
				'name' => sprintf(__('%s content', 'notification'), $commentTypeName),
				'description' => __('Great post!', 'notification'),
				'example' => true,
				'group' => $commentTypeName,
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->comment_content;
				},
			]
		);

		parent::__construct($args);
	}
}
