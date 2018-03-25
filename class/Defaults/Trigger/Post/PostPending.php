<?php
/**
 * Post sent for review trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;

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
		$this->{ $this->post_type } = $this->callback_args[1];

		if ( $this->{ $this->post_type }->post_type != $this->post_type ) {
			return false;
		}

		$this->author = get_userdata( $this->{ $this->post_type }->post_author );

		$this->{ $this->post_type . '_creation_datetime' }     = strtotime( $this->{ $this->post_type }->post_date );
		$this->{ $this->post_type . '_modification_datetime' } = strtotime( $this->{ $this->post_type }->post_modified );

		// Postpone the action to make sure all the meta has been saved.
		$this->postpone_action( 'save_post', 1000 );

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
