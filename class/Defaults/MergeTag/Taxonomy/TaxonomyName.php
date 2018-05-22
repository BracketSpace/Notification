<?php
/**
 * Taxonomy title merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Taxonomy object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Taxonomy title merge tag class
 */
class TaxonomyName extends StringTag {

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
    		$this->taxonomy = 'category';
    	}

    	$args = wp_parse_args( $params, array(
			'slug'        => $this->taxonomy . '_term_title',
			// translators: singular post name.
			'name'        => sprintf( __( '%s title', 'notification' ), $this->get_nicename() ),
			'description' => __( 'Hello World', 'notification' ),
			'example'     => true,
			'resolver'    => function() {
				return  $this->trigger->{ $this->taxonomy }->labels->name;
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
	 * Gets nice, translated post name
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
