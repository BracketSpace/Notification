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

		parent::__construct(
			array(
				'post_type' => $post_type,
				'slug'      => 'wordpress/' . $post_type . '/pending',
				// translators: singular post name.
				'name'      => sprintf( __( '%s sent for review', 'notification' ), parent::get_post_type_name( $post_type ) ),
			)
		);

		$this->add_action( 'transition_post_status', 10, 3 );

		// translators: 1. singular post name, 2. post type slug.
		$this->set_description( sprintf( __( 'Fires when %1$s (%2$s) is sent for review', 'notification' ), parent::get_post_type_name( $post_type ), $post_type ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @param string $new_status New post status.
	 * @param string $old_status Old post status.
	 * @param object $post       Post object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $new_status, $old_status, $post ) {

		$this->{ $this->post_type } = $post;

		if ( $this->{ $this->post_type }->post_type !== $this->post_type ) {
			return false;
		}

		if ( 'pending' === $old_status || 'pending' !== $new_status ) {
			return false;
		}

		$this->author = get_userdata( $this->{ $this->post_type }->post_author );

		$this->{ $this->post_type . '_creation_datetime' }     = strtotime( $this->{ $this->post_type }->post_date );
		$this->{ $this->post_type . '_modification_datetime' } = strtotime( $this->{ $this->post_type }->post_modified );

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
