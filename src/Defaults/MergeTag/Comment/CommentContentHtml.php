<?php

/**
 * Comment content html merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\HtmlTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment content html merge tag class
 */
class CommentContentHtml extends HtmlTag
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

		$commentTypeName = WpObjectHelper::get_comment_type_name($this->commentType);

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'comment_content_html',
				// Translators: Comment type name.
				'name' => sprintf(__('%s HTML content', 'notification'), $commentTypeName),
				'description' => __('Great post!', 'notification'),
				'example' => true,
				'group' => $commentTypeName,
				'resolver' => function ( $trigger ) {
					return $trigger->{ $this->getTriggerProp() }->commentContent;
				},
			]
		);

		parent::__construct($args);
	}
}
