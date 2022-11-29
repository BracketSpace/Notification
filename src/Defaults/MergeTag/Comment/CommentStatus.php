<?php

/**
 * Comment status merge tag
 *
 * @package notification
 */

declare(strict_types=1);

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
				'slug' => 'comment_status',
				// Translators: Comment type name.
				'name' => sprintf(__('%s status', 'notification'), $commentTypeName),
				'description' => __('Approved', 'notification'),
				'example' => true,
				'group' => $commentTypeName,
				'resolver' => function ( $trigger ) {
					if ($trigger->{ $this->getTriggerProp() === '1' }->commentApproved) {
						return __('Approved', 'notification');
					}

					if ($trigger->{ $this->getTriggerProp() === '0' }->commentApproved) {
						return __('Unapproved', 'notification');
					}

					if ($trigger->{ $this->getTriggerProp() === 'spam' }->commentApproved) {
						return __('Marked as spam', 'notification');
					}

					if ($trigger->{ $this->getTriggerProp() === 'trash' }->commentApproved) {
						return __('Trashed', 'notification');
					}
				},
			]
		);

		parent::__construct($args);
	}
}
