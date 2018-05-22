<?php
/**
 * Taxonomy added trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Taxonomy added trigger class
 */
class TaxonomyAdded extends TaxonomyTrigger {

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $taxonomy = 'category' ) {

		parent::__construct( array(
			'taxonomy'  => $taxonomy,
			'slug'      => 'wordpress/' . $taxonomy . '/added',
			'name'      => sprintf( __( '%s term added', 'notification' ), parent::get_taxonomy_name( $taxonomy ) ),
		) );

		$this->add_action( 'created_' . $taxonomy, 10, 2 );

		// translators: 1. singular post name, 2. post type slug.
		$this->set_description( sprintf( __( 'Fires when %s (%s) term is added to database. Useful when adding terms programatically or for 3rd party integration', 'notification' ), parent::get_taxonomy_name( $taxonomy ), $taxonomy ) );

	}

	/**
	 * Assigns action callback args to object
	 * Return `false` if you want to abort the trigger execution
	 *
     * @param int $term_id Term ID.
     * @param int $tt_id   Term taxonomy ID.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $term_id, $tt_id ) {

		$term_by_tax_id = get_term_by( 'term_taxonomy_id', $tt_id );
		$term = get_term( $term_id );

		if( $term_by_tax_id === $term ) {
			$this->{ $this->term } = $term;
		}

		// $this->author          = get_userdata( $this->{ $this->post_type }->post_author );
		// $this->publishing_user = get_userdata( get_current_user_id() );

		// $this->{ $this->post_type . '_creation_datetime' }     = strtotime( $this->{ $this->post_type }->post_date );
		// $this->{ $this->post_type . '_modification_datetime' } = strtotime( $this->{ $this->post_type }->post_modified );

	}

}
