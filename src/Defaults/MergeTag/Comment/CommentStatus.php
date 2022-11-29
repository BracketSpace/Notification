<?php

declare(strict_types=1);

/**
 * Comment status merge tag
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Comment;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Comment status merge tag class
 */
class CommentStatus extends StringTag
{

	/**
	 * Trigger property to get the comment data from
	 *
	 * @var string
	 */
	protected $comment_type = 'comment';

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

		$comment_type_name = WpObjectHelper::get_comment_type_name($this->comment_type);

		$args = wp_parse_args(
			$params,
			[
				'slug' => 'comment_status',
				// Translators: Comment type name.
				'name' => sprintf(__('%s status', 'notification'), $comment_type_name),
				'description' => __('Approved', 'notification'),
				'example' => true,
				'group' => $comment_type_name,
				'resolver' => function ( $trigger ) {
					if ($trigger->{ $this->get_trigger_prop() === '1' }->comment_approved) {
						return __('Approved', 'notification');
					}

					if ($trigger->{ $this->get_trigger_prop() === '0' }->comment_approved) {
						return __('Unapproved', 'notification');
					}

					if ($trigger->{ $this->get_trigger_prop() === 'spam' }->comment_approved) {
						return __('Marked as spam', 'notification');
					}

					if ($trigger->{ $this->get_trigger_prop() === 'trash' }->comment_approved) {
						return __('Trashed', 'notification');
					}
				},
			]
		);

		parent::__construct($args);
	}
}
