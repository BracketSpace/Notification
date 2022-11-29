<?php

/**
 * Comment action spam URL merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment action spam URL merge tag class
 */
class CommentActionSpam extends UrlTag
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
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] )
	{

		if (isset($params['comment_type']) && ! empty($params['comment_type'])) {
			$this->commentType = $params['comment_type'];
		}

		$this->setTriggerProp($params['property_name'] ?? $this->commentType);

		$commentTypeName = WpObjectHelper::getCommentTypeName($this->commentType);

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'comment_spam_action_url',
				// Translators: Comment type name.
				'name' => sprintf(__('%s mark as spam URL', 'notification'), $commentTypeName),
				// Translators: comment type actions text.
				'group' => sprintf(__('%s actions', 'notification'), $commentTypeName),
				'resolver' => function ( $trigger ) {
					return admin_url("comment.php?action=spam&c={$trigger->{ $this->getTriggerProp() }->commentID}#wpbody-content");
				},
			]
		);

		parent::__construct($args);
	}
}
