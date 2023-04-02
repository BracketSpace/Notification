<?php

/**
 * Comment action approve URL merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment action approve URL merge tag class
 */
class CommentActionApprove extends UrlTag
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
				'slug' => 'comment_approve_action_url',
				'name' => sprintf(
					// Translators: Comment type name.
					__(
						'%s approve URL',
						'notification'
					),
					$commentTypeName
				),
				'group' => sprintf(
					// Translators: comment type actions text.
					__(
						'%s actions',
						'notification'
					),
					$commentTypeName
				),
				'resolver' => function ($trigger) {
					return admin_url(
					// phpcs:ignore Generic.Files.LineLength.TooLong
						"comment.php?action=approve&c={$trigger->{ $this->getTriggerProp() }->comment_ID}#wpbody-content"
					);
				},
			]
		);

		parent::__construct($args);
	}
}
