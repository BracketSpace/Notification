<?php

/**
 * Comment author IP merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\IPTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment author IP merge tag class
 */
class CommentAuthorIP extends IPTag
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
				'slug' => 'comment_author_IP',
				'name' => sprintf(
				// Translators: Comment type name.
					__('%s author IP', 'notification'),
					$commentTypeName
				),
				'description' => '127.0.0.1',
				'example' => true,
				'group' => sprintf(
				// Translators: comment type author.
					__('%s author', 'notification'),
					$commentTypeName
				),
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->comment_author_IP;
				},
			]
		);

		parent::__construct($args);
	}
}
