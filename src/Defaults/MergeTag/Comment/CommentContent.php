<?php

/**
 * Comment content merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment content merge tag class
 */
class CommentContent extends StringTag
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
			$this->comment_type = $params['comment_type'];
		}

		$this->set_trigger_prop($params['property_name'] ?? $this->comment_type);

		$commentTypeName = WpObjectHelper::get_comment_type_name($this->comment_type);

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'comment_content',
				// Translators: Comment type name.
				'name' => sprintf(__('%s content', 'notification'), $commentTypeName),
				'description' => __('Great post!', 'notification'),
				'example' => true,
				'group' => $commentTypeName,
				'resolver' => function ( $trigger ) {
					return $trigger->{ $this->get_trigger_prop() }->comment_content;
				},
			]
		);

		parent::__construct($args);
	}
}
