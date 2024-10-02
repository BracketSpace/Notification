<?php

/**
 * Comment author URL merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\MergeTag\Comment;

use BracketSpace\Notification\Repository\MergeTag\UrlTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment author URL merge tag class
 */
class CommentAuthorUrl extends UrlTag
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
				'slug' => 'comment_author_url',
				// Translators: Comment type name.
				'name' => sprintf(__('%s author URL', 'notification'), $commentTypeName),
				'description' => __('http://mywebsite.com', 'notification'),
				'example' => true,
				// Translators: comment type author.
				'group' => sprintf(__('%s author', 'notification'), $commentTypeName),
				'resolver' => function ($trigger) {
					return $trigger->{$this->getTriggerProp()}->comment_author_url;
				},
			]
		);

		parent::__construct($args);
	}
}
