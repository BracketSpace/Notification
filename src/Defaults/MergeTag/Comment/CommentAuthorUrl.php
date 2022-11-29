<?php

/**
 * Comment author URL merge tag
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;
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
				'slug' => 'comment_author_url',
				// Translators: Comment type name.
				'name' => sprintf(__('%s author URL', 'notification'), $commentTypeName),
				'description' => __('http://mywebsite.com', 'notification'),
				'example' => true,
				// Translators: comment type author.
				'group' => sprintf(__('%s author', 'notification'), $commentTypeName),
				'resolver' => function ( $trigger ) {
					return $trigger->{ $this->get_trigger_prop() }->comment_author_url;
				},
			]
		);

		parent::__construct($args);
	}
}
