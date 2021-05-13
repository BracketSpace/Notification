<?php
/**
 * Taxonomy trigger abstract
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Taxonomy;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Traits;

/**
 * Taxonomy trigger class
 */
abstract class TermTrigger extends Abstracts\Trigger {

	use Traits\TaxonomyUtils;

	/**
	 * Taxonomy slug
	 *
	 * @var string
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
	public $term_permalink;

	/**
	 * Constructor
	 *
	 * @param array $params trigger configuration params.
	 */
	public function __construct( $params = [] ) {

		if ( ! isset( $params['taxonomy'], $params['slug'], $params['name'] ) ) {
			trigger_error( 'TaxonomyTrigger requires taxonomy slug, slug and name.', E_USER_ERROR );
		}

		$this->taxonomy = $params['taxonomy'];

		parent::__construct( $params['slug'], $params['name'] );

		$this->set_group( $this->get_current_taxonomy_name() );

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\Taxonomy\TermID() );
		$this->add_merge_tag( new MergeTag\Taxonomy\TermDescription() );
		$this->add_merge_tag( new MergeTag\Taxonomy\TermName() );
		$this->add_merge_tag( new MergeTag\Taxonomy\TermSlug() );
		$this->add_merge_tag( new MergeTag\Taxonomy\TermPermalink() );

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomyName( [
			'taxonomy' => $this->taxonomy,
		] ) );

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomySlug( [
			'taxonomy' => $this->taxonomy,
		] ) );

	}

}
