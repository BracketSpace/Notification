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
	 * @since 5.0.0
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = [] )
	{

		if (isset($params['comment_type']) && ! empty($params['comment_type'])) {
			$this->commentType = $params['comment_type'];
		}

		$this->setTriggerProp($params['property_name'] ?? $this->commentType);

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'comment_type',
				'name' => __('Comment type', 'notification'),
				'description' => __('Comment or Pingback or Trackback or Custom', 'notification'),
				'group' => WpObjectHelper::get_comment_type_name($this->commentType),
				'resolver' => function ( $trigger ) {
					return get_comment_type($trigger->{ $this->getTriggerProp() });
				},
			]
		);

		parent::__construct($args);
	}
}
