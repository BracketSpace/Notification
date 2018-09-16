<?php
/**
 * Taxonomy slug merge tag
 *
 * Requirements:
 * - Trigger property of the WP_Taxonomy term object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Taxonomy slug merge tag class
 */
class TaxonomySlug extends StringTag {

	/**
	 * Taxonomy slug
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Merge tag constructor
	 *
	 * @since 5.2.2
	 * @param array $params merge tag configuration params.
	 */
	public function __construct( $params = array() ) {

		if ( isset( $params['taxonomy'] ) ) {
			$this->taxonomy = $params['taxonomy'];
		} else {
			$this->taxonomy = 'category';
		}

		$args = wp_parse_args(
			$params,
			array(
				'slug'        => $this->taxonomy . '_slug',
				'name'        => __( 'Taxonomy slug', 'notification' ),
				'description' => __( 'hello-world', 'notification' ),
				'example'     => true,
				'resolver'    => function( $trigger ) {
					return $trigger->taxonomy;
				},
			)
		);

		parent::__construct( $args );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements() {
		return isset( $this->trigger->taxonomy );
	}

}
