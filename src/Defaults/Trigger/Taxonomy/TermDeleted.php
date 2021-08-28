<?php
/**
 * Taxonomy term deleted trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Taxonomy term deleted trigger class
 */
class TermDeleted extends TermTrigger {

	/**
	 * Term deletion date and time
	 *
	 * @var string
	 */
	public $term_deletion_datetime;

	/**
	 * Constructor
	 *
	 * @param string $taxonomy optional, default: category.
	 */
	public function __construct( $taxonomy = 'category' ) {
		$this->taxonomy = $taxonomy;

		parent::__construct( [
			'taxonomy' => $taxonomy,
			'slug'     => 'taxonomy/' . $taxonomy . '/deleted',
		] );

		$this->add_action( 'pre_delete_term', 100, 4 );
	}

	/**
	 * Lazy loads the name
	 *
	 * @return string name
	 */
	public function get_name() : string {
		// Translators: taxonomy name.
		return sprintf( __( '%s term deleted', 'notification' ), WpObjectHelper::get_taxonomy_name( $this->taxonomy ) );
	}

	/**
	 * Lazy loads the description
	 *
	 * @return string description
	 */
	public function get_description() : string {
		return sprintf(
			// Translators: 1. taxonomy name, 2. taxonomy slug.
			__( 'Fires when %1$s (%2$s) is deleted', 'notification' ),
			WpObjectHelper::get_taxonomy_name( $this->taxonomy ),
			$this->taxonomy
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param integer $term_id Term ID.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $term_id ) {

		$term       = get_term( $term_id );
		$this->term = $term;

		if ( $this->taxonomy !== $this->term->taxonomy ) {
			return false;
		}

		$this->taxonomy       = $this->term->taxonomy;
		$this->term_permalink = get_term_link( $this->term );

		$this->term_deletion_datetime = time();

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug'  => 'term_deletion_datetime',
			'name'  => __( 'Term deletion date and time', 'notification' ),
			'group' => __( 'Term', 'notification' ),
		] ) );

	}

}
