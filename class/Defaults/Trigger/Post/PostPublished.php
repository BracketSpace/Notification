<?php
/**
 * Post published trigger
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Trigger\Post;

use underDEV\Notification\Defaults\MergeTag;

/**
 * Post published trigger class
 */
class PostPublished extends PostTrigger {

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $post_type = 'post' ) {

		parent::__construct( array(
			'post_type' => $post_type,
			'slug'      => 'wordpress/' . $post_type . '/published',
			// translators: singular post name.
			'name'      => sprintf( __( '%s published' ), parent::get_post_type_name( $post_type ) ),
		) );

		$this->add_action( 'transition_post_status', 10, 3 );

		// translators: 1. singular post name, 2. post type slug.
		$this->set_description( sprintf( __( 'Fires when %s (%s) is published' ), parent::get_post_type_name( $post_type ), $post_type ) );

	}

	/**
	 * Assigns action callback args to object
	 * Return `false` if you want to abort the trigger execution
	 *
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action() {

		$new_status = $this->callback_args[0];
		$old_status = $this->callback_args[1];
		// WP_Post object.
		$this->post = $this->callback_args[2];

		if ( $this->post->post_type != $this->post_type ) {
			return false;
		}

		if ( $new_status == $old_status ) {
			return false;
		}

		if ( $new_status != 'publish' ) {
			return false;
		}

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\Post\PostID() );

    }

}
