<?php
/**
 * Taxonomy slug merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Taxonomy object
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
	 * Taxonomy Type slug
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
     * Merge tag constructor
     *
     * @since 5.0.0
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	if ( isset( $params['taxonomy'] ) ) {
    		$this->taxonomy = $params['taxonomy'];
    	} else {
    		$this->taxonomy = 'post';
    	}

    	$args = wp_parse_args( $params, array(
			'slug'        => $this->taxonomy . '_slug',
			// translators: singular post name.
			'name'        => sprintf( __( '%s slug', 'notification' ), $this->get_nicename() ),
			'description' => __( 'hello-world', 'notification' ),
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->{ $this->taxonomy }->post_name;
			},
		) );

    	parent::__construct( $args );

	}

	/**
	 * Function for checking requirements
	 *
	 * @return boolean
	 */
	public function check_requirements() {
		return isset( $this->trigger->{ $this->taxonomy } );
	}

	/**
	 * Gets nice, translated taxonomy name
	 *
	 * @since  5.0.0
	 * @return string post name
	 */
	public function get_nicename() {
		$taxonomy = get_taxonomy( $this->taxonomy );
		if ( empty( $taxonomy ) ) {
			return '';
		}
		return $taxonomy->labels->singular_name;
	}

}
