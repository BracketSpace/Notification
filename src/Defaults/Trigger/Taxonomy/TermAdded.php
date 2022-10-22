<?php
/**
 * Taxonomy term added trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Taxonomy term added trigger class
 */
class TermAdded extends TermTrigger {

	/**
	 * Term creation date and time
	 *
	 * @var string
	 */
	public $term_creation_datetime;

	/**
	 * Constructor
	 *
	 * @param string $taxonomy optional default category.
	 */
	public function __construct( $taxonomy = 'category' ) {
		$this->taxonomy = WpObjectHelper::get_taxonomy( $taxonomy );

		parent::__construct( [
			'taxonomy' => $taxonomy,
			'slug'     => 'taxonomy/' . $taxonomy . '/created',
		] );

		$this->add_action( 'created_' . $taxonomy, 100, 2 );
	}

	/**
	 * Lazy loads the name
	 *
	 * @return string name
	 */
	public function get_name() : string {
		// Translators: taxonomy name.
		return sprintf( __( '%s term created', 'notification' ), $this->taxonomy->labels->singular_name ?? '' );
	}

	/**
	 * Lazy loads the description
	 *
	 * @return string description
	 */
	public function get_description() : string {
		return sprintf(
			// Translators: 1. taxonomy name, 2. taxonomy slug.
			__( 'Fires when %1$s (%2$s) is created', 'notification' ),
			$this->taxonomy->labels->singular_name ?? '',
			$this->taxonomy->name ?? ''
		);
	}

	/**
	 * Sets trigger's context
	 * Return `false` if you want to abort the trigger execution
	 *
	 * @param integer $term_id Term ID.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $term_id ) {
		$term = get_term( $term_id );

		if ( ! ( $this->taxonomy instanceof \WP_Taxonomy ) || ! ( $term instanceof \WP_Term ) ) {
			return false;
		}

		$this->term = $term;

		if ( $this->taxonomy->name !== $this->term->taxonomy ) {
			return false;
		}

		$term_link            = get_term_link( $this->term );
		$this->term_permalink = is_string( $term_link ) ? $term_link : '';

		$this->term_creation_datetime = (string) time();

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug'  => 'term_creation_datetime',
			'name'  => __( 'Term creation date and time', 'notification' ),
			'group' => __( 'Term', 'notification' ),
		] ) );

	}

}
