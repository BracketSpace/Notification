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
			'name'      => sprintf( __( '%s sent for review', 'notification' ), parent::get_post_type_name( $post_type ) ),
		) );

		$this->add_action( 'pending_' . $post_type, 10, 2 );

		// translators: 1. singular post name, 2. post type slug.
		$this->set_description( sprintf( __( 'Fires when %s (%s) is sent for review', 'notification' ), parent::get_post_type_name( $post_type ), $post_type ) );

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

		$this->author = get_userdata( $this->post->post_author );

		$this->{ $this->post_type . '_creation_datetime' }     = strtotime( $this->post->post_date );
		$this->{ $this->post_type . '_modification_datetime' } = strtotime( $this->post->post_modified );

		/**
		 * ACF integration
		 * If ACF is active and the action hasn't been postponed yet,
		 * we are aborting this action and hook to the later one,
		 * after ACF saves the fields.
		 */
		if ( function_exists( 'acf' ) ) {
			$this->postpone_action( 'acf/save_post', 1000 );
		}

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$post_name = parent::get_post_type_name( $this->post_type );

		parent::merge_tags();

    }

}
