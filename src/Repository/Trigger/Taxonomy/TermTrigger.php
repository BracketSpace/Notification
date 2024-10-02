<?php

/**
 * Taxonomy trigger abstract
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Repository\Trigger\Taxonomy;

use BracketSpace\Notification\Repository\Trigger\BaseTrigger;
use BracketSpace\Notification\Repository\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Taxonomy trigger class
 */
abstract class TermTrigger extends BaseTrigger
{
	/**
	 * Taxonomy slug
	 *
	 * @var \WP_Taxonomy|null
	 */
	public $taxonomy;

	/**
	 * Term object
	 *
	 * @var \WP_Term
	 */
	public $term;

	/**
	 * Term permalink
	 *
	 * @var string
	 */
	public $termPermalink = '';

	/**
	 * Constructor
	 *
	 * @param array<mixed> $params trigger configuration params.
	 */
	public function __construct($params = [])
	{
		if (!isset($params['taxonomy'], $params['slug'])) {
			trigger_error('TaxonomyTrigger requires taxonomy slug and trigger slug.', E_USER_ERROR);
		}

		$this->taxonomy = WpObjectHelper::getTaxonomy($params['taxonomy']);

		parent::__construct($params['slug']);
	}

	/**
	 * Lazy loads group name
	 *
	 * @return string|null Group name
	 */
	public function getGroup()
	{
		return $this->taxonomy->labels->singular_name ?? '';
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function mergeTags()
	{
		$this->addMergeTag(new MergeTag\Taxonomy\TermID());
		$this->addMergeTag(new MergeTag\Taxonomy\TermDescription());
		$this->addMergeTag(new MergeTag\Taxonomy\TermName());
		$this->addMergeTag(new MergeTag\Taxonomy\TermSlug());
		$this->addMergeTag(new MergeTag\Taxonomy\TermPermalink());

		$this->addMergeTag(
			new MergeTag\Taxonomy\TaxonomyName(
				[
					'tag_name' => $this->taxonomy->name ?? '',
					'property_name' => 'taxonomy',
				]
			)
		);

		$this->addMergeTag(
			new MergeTag\Taxonomy\TaxonomySlug(
				[
					'tag_name' => $this->taxonomy->name ?? '',
					'property_name' => 'taxonomy',
				]
			)
		);
	}
}
