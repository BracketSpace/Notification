<?php

/**
 * Taxonomy term updated trigger
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\Taxonomy;

use BracketSpace\Notification\Repository\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Taxonomy updated trigger class
 */
class TermUpdated extends TermTrigger
{
	/**
	 * Term modification date and time
	 *
	 * @var string
	 */
	public $termModificationDatetime;

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
				'slug' => 'taxonomy/' . $taxonomy . '/updated',
			]
		);

		$this->addAction('edited_term', 100, 2);
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
			__('%s term updated', 'notification'),
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
			__('Fires when %1$s (%2$s) is updated', 'notification'),
			$this->taxonomy->labels->singular_name ?? '',
			$this->taxonomy->name ?? ''
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param int $termId Term ID used only due to lack of taxonomy param.
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
		$this->termPermalink = is_string($termLink) ? $termLink : '';

		$this->termModificationDatetime = (string)time();
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
					'slug' => 'term_modification_datetime',
					'name' => __('Term modification date and time', 'notification'),
					'group' => __('Term', 'notification'),
				]
			)
		);
	}
}
