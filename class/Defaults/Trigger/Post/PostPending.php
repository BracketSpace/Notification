<?php
/**
 * Post sent for review trigger
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\Trigger\Post;

use underDEV\Notification\Defaults\MergeTag;

/**
 * Post sent for review trigger class
 */
class PostPending extends PostTrigger {

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $post_type = 'post' ) {

		parent::__construct( array(
			'post_type' => $post_type,
			'slug'      => 'wordpress/' . $post_type . '/pending',
			// translators: singular post name.
			'name'      => sprintf( __( '%s sent for review' ), parent::get_post_type_name( $post_type ) ),
		) );

		$this->add_action( 'pending_' . $post_type, 10, 2 );

		// translators: 1. singular post name, 2. post type slug.
		$this->set_description( sprintf( __( 'Fires when %s (%s) is sent for review' ), parent::get_post_type_name( $post_type ), $post_type ) );

	}

	/**
	 * Assigns action callback args to object
	 * Return `false` if you want to abort the trigger execution
	 *
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action() {

		$post_id = $this->callback_args[0];
		// WP_Post object.
		$this->post = $this->callback_args[1];

		if ( $this->post->post_type != $this->post_type ) {
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
		$this->add_merge_tag( new MergeTag\Post\PostPermalink() );
		$this->add_merge_tag( new MergeTag\Post\PostTitle() );
		$this->add_merge_tag( new MergeTag\Post\PostSlug() );

    }

}
