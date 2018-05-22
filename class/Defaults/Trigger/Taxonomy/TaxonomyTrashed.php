<?php
/**
 * Taxonomy trashed trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Taxonomy trashed trigger class
 */
class TaxonomyTrashed extends TaxonomyTrigger {

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $taxonomy = 'category' ) {

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
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $term, $taxonomy ) {

		$this->term = get_term( $term );
		$this->{ $this->taxonomy } = $taxonomy;

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$taxonomy_name = parent::get_taxonomy_name( $this->taxonomy );
		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\Taxonomy\TermID( array(
			'taxonomy' => $this->{ $this->taxonomy },
			'term' => $this->term,
		) ) );

    }

}
