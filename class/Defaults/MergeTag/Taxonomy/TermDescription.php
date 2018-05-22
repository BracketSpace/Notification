<?php
/**
 * Taxonomy content merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Taxonomy object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Taxonomy;

use BracketSpace\Notification\Defaults\MergeTag\StringTag;


/**
 * Taxonomy content merge tag class
 */
class TermDescription extends StringTag {

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
			'slug'        => $this->taxonomy . '_term_description',
			// translators: singular post name.
			'name'        => sprintf( __( '%s term description', 'notification' ), $this->get_nicename() ),
			'description' => __( 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 'notification' ),
			'example'     => true,
			'resolver'    => function() {
				return $this->trigger->{ $this->term }->description;
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
		return isset( $this->trigger->{ $this->term }->description );
	}

	/**
	 * Gets nice, translated post name
	 *
	 * @since  5.0.0
	 * @return string post name
	 */
	public function get_nicename() {
		$term = get_term( $this->term );
		if ( empty( $term ) ) {
			return '';
		}
		return $term->name;
	}

}
