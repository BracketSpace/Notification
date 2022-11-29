<?php

/**
 * Comment is reply merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment is reply merge tag class
 */
class CommentIsReply extends StringTag
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
				'slug' => 'comment_is_reply',
				// Translators: Comment type name.
				'name' => sprintf(__('Is %s a reply?', 'notification'), $commentTypeName),
				'description' => __('Yes or No', 'notification'),
				'example' => true,
				'group' => $commentTypeName,
				'resolver' => function ( $trigger ) {
					$hasParent = $trigger->{ $this->getTriggerProp() }->commentParent;
					return $hasParent ? __('Yes', 'notification') : __('No', 'notification');
				},
			]
		);

		parent::__construct($args);
	}
}
