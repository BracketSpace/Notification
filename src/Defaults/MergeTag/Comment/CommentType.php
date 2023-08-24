<?php

/**
 * Comment type merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment type merge tag class
 */
class CommentType extends StringTag
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

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'comment_type',
				'name' => __('Comment type', 'notification'),
				'description' => __('Comment or Pingback or Trackback or Custom', 'notification'),
				'group' => WpObjectHelper::getCommentTypeName($this->commentType),
				'resolver' => function ($trigger) {
					return get_comment_type($trigger->{$this->getTriggerProp()});
				},
			]
		);

		parent::__construct($args);
	}
}
