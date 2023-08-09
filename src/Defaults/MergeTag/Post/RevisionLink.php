<?php

/**
 * Revision link merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\UrlTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Revision link merge tag class
 */
class RevisionLink extends UrlTag
{
	/**
	 * Merge tag constructor
	 *
	 * @param array<mixed> $params merge tag configuration params.
	 * @since 5.0.0
	 */
	public function __construct($params = [])
	{

		$this->setTriggerProp($params['post_type'] ?? 'post');

		$postTypeName = WpObjectHelper::getPostTypeName($this->getTriggerProp());

		$args = wp_parse_args(
			$params,
			[
				'slug' => sprintf('%s_revision_link', $this->getTriggerProp()),
				// translators: singular post name.
				'name' => sprintf(__('%s revision link', 'notification'), $postTypeName),
				'description' => __('https://example.com/wp-admin/revision.php?revision=id', 'notification'),
				'example' => true,
				'group' => $postTypeName,
				'resolver' => function ($trigger) {
					$revisionsId = wp_get_post_revisions(
						$trigger->{$this->getTriggerProp()}->ID,
						[
							'orderby' => 'ID',
							'order' => 'DESC',
							'fields' => 'ids',
						]
					);

					return !empty($revisionsId)
						? sprintf(
							admin_url('revision.php?revision=%s'),
							$revisionsId[0]
						)
						: '';
				},
			]
		);

		parent::__construct($args);
	}
}
