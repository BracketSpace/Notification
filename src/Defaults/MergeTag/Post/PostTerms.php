<?php

/**
 * Post terms merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post terms merge tag class
 */
class PostTerms extends StringTag
{
	/**
	 * Post Taxonomy Object
	 *
	 * @var object
	 */
	protected $taxonomy;

	/**
	 * Merge tag constructor
	 *
	 * @param array $params merge tag configuration params.
	 * @since 5.1.3
	 */
	public function __construct($params = [])
	{

		$this->setTriggerProp($params['post_type'] ?? 'post');

		if (isset($params['taxonomy'])) {
			$this->taxonomy = is_string($params['taxonomy'])
				? get_taxonomy($params['taxonomy'])
				: $params['taxonomy'];
		}

		$postTypeName = WpObjectHelper::getPostTypeName($this->getTriggerProp());

		$args = wp_parse_args(
			$params,
			[
				'slug' => sprintf(
					'%s_%s',
					$this->getTriggerProp(),
					$this->taxonomy->name
				),
				// translators: 1. Post Type 2. Taxonomy name.
				'name' => sprintf(
					__(
						'%1$s %2$s',
						'notification'
					),
					$postTypeName,
					$this->taxonomy->label
				),
				'description' => __(
					'General, Tech, Lifestyle',
					'notification'
				),
				'example' => true,
				'group' => $postTypeName,
				'resolver' => function ($trigger) {
					$postTerms = get_the_terms(
						$trigger->{$this->getTriggerProp()},
						$this->taxonomy->name
					);
					if (empty($postTerms) || is_wp_error($postTerms)) {
						return '';
					}

					return implode(
						', ',
						wp_list_pluck(
							$postTerms,
							'name'
						)
					);
				},
			]
		);

		parent::__construct($args);
	}
}
