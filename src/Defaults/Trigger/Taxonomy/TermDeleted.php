<?php

/**
 * Taxonomy term deleted trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Defaults\Trigger\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Taxonomy term deleted trigger class
 */
class TermDeleted extends TermTrigger
{
	/**
	 * Term deletion date and time
	 *
	 * @var string
	 */
	public $termDeletionDatetime;

	/**
	 * Constructor
	 *
	 * @param string $taxonomy optional, default: category.
	 */
	public function __construct($taxonomy = 'category')
	{
		$this->taxonomy = WpObjectHelper::getTaxonomy($taxonomy);

		parent::__construct(
			[
				'taxonomy' => $taxonomy,
				'slug' => 'taxonomy/' . $taxonomy . '/deleted',
			]
		);

		$this->addAction(
			'pre_delete_term',
			100,
			4
		);
	}

	/**
	 * Lazy loads the name
	 *
	 * @return string name
	 */
	public function getName(): string
	{
		return sprintf(
		// Translators: taxonomy name.
			__('%s term deleted', 'notification'),
			$this->taxonomy->labels->singular_name ?? ''
		);
	}

	/**
	 * Lazy loads the description
	 *
	 * @return string description
	 */
	public function getDescription(): string
	{
		return sprintf(
		// Translators: 1. taxonomy name, 2. taxonomy slug.
			__('Fires when %1$s (%2$s) is deleted', 'notification'),
			$this->taxonomy->labels->singular_name ?? '',
			$this->taxonomy->name ?? ''
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $termId Term ID.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context($termId)
	{
		$term = get_term($termId);

		if (!($this->taxonomy instanceof \WP_Taxonomy) || !($term instanceof \WP_Term)) {
			return false;
		}

		$this->term = $term;

		if ($this->taxonomy->name !== $this->term->taxonomy) {
			return false;
		}

		$termLink = get_term_link($this->term);
		$this->termPermalink = is_string($termLink)
			? $termLink
			: '';

		$this->termDeletionDatetime = (string)time();
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		parent::mergeTags();

		$this->addMergeTag(
			new MergeTag\DateTime\DateTime(
				[
					'slug' => 'term_deletion_datetime',
					'name' => __('Term deletion date and time', 'notification'),
					'group' => __('Term', 'notification'),
				]
			)
		);
	}
}
