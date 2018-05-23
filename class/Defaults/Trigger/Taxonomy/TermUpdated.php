<?php
/**
 * Taxonomy term updated trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Taxonomy updated trigger class
 */
class TermUpdated extends TermTrigger {

	/**
	 * Term object
	 *
	 * @var object
	 */
	public $term;

	/**
	 * Taxonomy slug
	 *
	 * @var string
	 */
	public $taxonomy;

	/**
	 * Constructor
	 *
	 * @param string $taxonomy optional, default: category.
	 */
	public function __construct( $taxonomy = 'category' ) {

		$this->taxonomy = $taxonomy;

		parent::__construct( array(
			'taxonomy' => $taxonomy,
			'slug'      => 'wordpress/' . $taxonomy . '/updated',
			'name'      => sprintf( __( '%s term updated', 'notification' ), parent::get_taxonomy_name( $taxonomy ) ),
		) );

		$this->add_action( 'edited_term', 10, 2 );

		// translators: 1. singular post name, 2. post type slug.
		$this->set_description( sprintf( __( 'Fires when %s (%s) term is updated', 'notification' ), parent::get_taxonomy_name( $taxonomy ), $taxonomy ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @param integer $term_id     Term ID used only due to lack of taxonomy param.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $term_id ) {

		$term = get_term( $term_id );
		$this->term = $term;

		if ( $this->taxonomy != $this->term->taxonomy ) {
			return false;
		}

		$this->taxonomy = $this->term->taxonomy;
		$this->term_permalink = get_term_link( $this->term );
		$this->field = get_field( 'tresc', $this->taxonomy . '_' . $term_id );

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
		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomyName() );
		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomySlug() );
		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => 'term_modification_datetime',
			'name' => sprintf( __( 'Term modification date and time', 'notification' ) ),
		) ) );
		parent::merge_tags();

    }

}
