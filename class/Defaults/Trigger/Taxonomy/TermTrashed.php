<?php
/**
 * Taxonomy term trashed trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Taxonomy trashed trigger class
 */
class TermTrashed extends TermTrigger {

	public $term;
	public $taxonomy;

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $taxonomy = 'category' ) {

		$this->taxonomy = $taxonomy;

		parent::__construct( array(
			'taxonomy' => $taxonomy,
			'slug'      => 'wordpress/' . $taxonomy . '/trashed',
			'name'      => sprintf( __( '%s term trashed', 'notification' ), parent::get_taxonomy_name( $taxonomy ) ),
		) );

		$this->add_action( 'pre_delete_term', 10, 4 );

		// translators: 1. singular post name, 2. post type slug.
		$this->set_description( sprintf( __( 'Fires when %s (%s) term is moved to trash', 'notification' ), parent::get_taxonomy_name( $taxonomy ), $taxonomy ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @param int    $term_id  Term ID.
	 * @param string $taxonomy Taxonomy slug.
	 *
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
		$this->add_merge_tag( new MergeTag\Taxonomy\TermField() );
		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => 'term_deletion_datetime',
			'name' => sprintf( __( 'Term deletion date and time', 'notification' ) ),
		) ) );
		parent::merge_tags();

    }

}
