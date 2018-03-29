<?php
/**
 * Post type merge tag
 *
 * Requirements:
 * - Trigger property of the post type slug with WP_Post object
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\MergeTag\Post;

use BracketSpace\Notification\Defaults\MergeTag\IntegerTag;


/**
 * Post type merge tag class
 */
class PostType extends StringTag {

	/**
	 * Post Type slug
	 *
	 * @var string
	 */
	protected $post_type;

	/**
     * Merge tag constructor
     *
     * @since 5.0.0
     * @param array $params merge tag configuration params.
     */
    public function __construct( $params = array() ) {

    	if ( isset( $params['post_type'] ) ) {
    		$this->post_type = $params['post_type'];
    	} else {
    		$this->post_type = 'post';
    	}

    	$args = wp_parse_args( $params, array(
			'slug'        => 'post_type',
			'name'        => __( 'Post Type', 'notification' ),
			'description' => 'post',
			'example'     => true,
			'resolver'    => function( $trigger ) {
				return $trigger->post_type;
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
		return isset( $this->trigger->{ $this->post_type }->ID );
	}

}
