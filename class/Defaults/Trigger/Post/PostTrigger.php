<?php
/**
 * Post trigger abstract
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Trigger\Post;

use underDEV\Notification\Abstracts;

/**
 * Post trigger class
 */
abstract class PostTrigger extends Abstracts\Trigger {

	/**
	 * Post Type slug
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Constructor
	 *
	 * @param array $params trigger configuration params.
	 */
	public function __construct( $params = array() ) {

		if ( ! isset( $params['post_type'], $params['slug'], $params['name'] ) ) {
			trigger_error( 'PostTrigger requires post_type, slug and name params.', E_USER_ERROR );
		}

		$this->post_type = $params['post_type'];

		parent::__construct( $params['slug'], $params['name'] );

		$this->set_group( $this->get_current_post_type_name() );

	}

	/**
	 * Gets nice, translated post name
	 *
	 * @since  [Next]
	 * @return string post name
	 */
	public function get_current_post_type_name() {
		return self::get_post_type_name( $this->post_type );
	}

	/**
	 * Gets nice, translated post name for post type slug
	 *
	 * @since  [Next]
	 * @param string $post_type post type slug.
	 * @return string post name
	 */
	public static function get_post_type_name( $post_type ) {
		return get_post_type_object( $post_type )->labels->singular_name;
	}

}
