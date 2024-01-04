<?php

/**
 * Comment author user agent merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment author user agent tag class
 */
class CommentAuthorUserAgent extends StringTag
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
				'slug' => 'comment_author_user_agent',
				// Translators: Comment type name.
				'name' => sprintf(__('%s author user browser agent', 'notification'), $commentTypeName),
				'description' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0',
				'example' => true,
				// Translators: comment type author.
				'group' => sprintf(__('%s author', 'notification'), $commentTypeName),
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->comment_agent;
				},
			]
		);

		parent::__construct($args);
	}
}
