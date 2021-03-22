<?php
/**
 * Taxonomy utilities.
 *
 * @package notification
 */

namespace BracketSpace\Notification\Traits;

/**
 * TaxonomyUtils trait
 */
trait TaxonomyUtils {

	/**
	 * Gets nice, translated taxonomy name
	 *
	 * @since  7.0.0
	 * @return string taxonomy
	 */
	public function get_current_taxonomy_name() {
		return self::get_taxonomy_name( $this->get_taxonomy() );
	}

	/**
	 * Gets nice, translated taxonomy name for taxonomy slug
	 *
	 * @since  7.0.0
	 * @param  string $taxonomy Taxonomy slug.
	 * @return string
	 */
	public static function get_taxonomy_name( $taxonomy ) {
		$taxonomies = notification_cache( 'taxonomies' );
		return $taxonomies[ $taxonomy ] ?? '';
	}

	/**
	 * Gets taxonomy slug
	 *
	 * @since  7.0.0
	 * @return string
	 */
	public function get_taxonomy() {
		return $this->taxonomy;
	}

}
