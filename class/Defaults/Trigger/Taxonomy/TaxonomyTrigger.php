<?php
/**
 * Taxonomy trigger abstract
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Taxonomy;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Taxonomy trigger class
 */
abstract class TaxonomyTrigger extends Abstracts\Trigger {

	/**
	 * Taxonomy Type slug
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Constructor
	 *
	 * @param array $params trigger configuration params.
	 */
	public function __construct( $params = array() ) {

		if ( ! isset( $params['taxonomy'], $params['slug'], $params['name'] ) ) {
			trigger_error( 'TaxonomyTrigger requires taxonomy slug and name.', E_USER_ERROR );
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

		$taxonomy_name = $this->get_current_taxonomy_name();

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomyID( array(
			'taxonomy' => $this->taxonomy,
		) ) );

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomyPermalink( array(
			'taxonomy' => $this->taxonomy,
		) ) );

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomyName( array(
			'taxonomy' => $this->taxonomy,
		) ) );

		$this->add_merge_tag( new MergeTag\Taxonomy\TaxonomySlug( array(
			'taxonomy' => $this->taxonomy,
		) ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => $this->taxonomy . '_creation_datetime',
			// translators: singular post name.
			'name' => sprintf( __( '%s creation date and time', 'notification' ), $taxonomy_name ),
		) ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( array(
			'slug' => $this->taxonomy . '_modification_datetime',
			// translators: singular post name.
			'name' => sprintf( __( '%s modification date and time', 'notification' ), $taxonomy_name ),
		) ) );

    }

	/**
	 * Gets nice, translated post name
	 *
	 * @since  5.0.0
	 * @return string post name
	 */
	public function get_current_taxonomy_name() {
		return self::get_taxonomy_name( $this->taxonomy );
	}

	/**
	 * Gets nice, translated post name for post type slug
	 *
	 * @since  5.0.0
	 * @param string $taxonomy post type slug.
	 * @return string post name
	 */
	public static function get_taxonomy_name( $taxonomy ) {
		return get_taxonomy( $taxonomy )->labels->name;
	}

}
