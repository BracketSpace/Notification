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
				'slug' => 'comment_status',
				'name' => sprintf(
					// Translators: Comment type name.
					__('%s status', 'notification'),
					$commentTypeName
				),
				'description' => __('Approved', 'notification'),
				'example' => true,
				'group' => $commentTypeName,
				'resolver' => function ($trigger) {
					if ($trigger->{$this->getTriggerProp() === '1'}->comment_approved) {
						return __('Approved', 'notification');
					}

					if ($trigger->{$this->getTriggerProp() === '0'}->comment_approved) {
						return __('Unapproved', 'notification');
					}

					if ($trigger->{$this->getTriggerProp() === 'spam'}->comment_approved) {
						return __('Marked as spam', 'notification');
					}

					if ($trigger->{$this->getTriggerProp() === 'trash'}->comment_approved) {
						return __('Trashed', 'notification');
					}
				},
			]
		);

		parent::__construct($args);
	}
}
