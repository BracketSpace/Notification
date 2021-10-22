<?php
/**
 * Taxonomy trigger abstract
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Taxonomy;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Taxonomy trigger class
 */
abstract class TermTrigger extends Abstracts\Trigger {

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
		if ( ! isset( $params['taxonomy'], $params['slug'] ) ) {
			trigger_error( 'TaxonomyTrigger requires taxonomy slug and trigger slug.', E_USER_ERROR );
		}

		$this->taxonomy = $params['taxonomy'];

		parent::__construct( $params['slug'] );
	}

	/**
	 * Lazy loads group name
	 *
	 * @return string|null Group name
	 */
	public function get_group() {
		return WpObjectHelper::get_taxonomy_name( $this->taxonomy );
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
