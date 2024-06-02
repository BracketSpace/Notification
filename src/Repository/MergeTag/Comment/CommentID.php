<?php

/**
 * Comment ID merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\Comment;

use BracketSpace\Notification\Repository\MergeTag\IntegerTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment ID merge tag class
 */
class CommentID extends IntegerTag
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
				'slug' => 'comment_ID',
				// Translators: Comment type name.
				'name' => sprintf(__('%s ID', 'notification'), $commentTypeName),
				'description' => '35',
				'example' => true,
				'group' => $commentTypeName,
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->comment_ID;
				},
			]
		);

		parent::__construct($args);
	}
}
