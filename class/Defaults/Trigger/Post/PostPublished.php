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

		$this->add_action( 'auto-draft_to_publish', 10 );
		$this->add_action( 'draft_to_publish', 10 );
		$this->add_action( 'pending_to_publish', 10 );
		$this->add_action( 'private_to_publish', 10 );
		$this->add_action( 'future_to_publish', 10 );

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

		// WP_Post object.
		$this->post = $this->callback_args[0];

		if ( $this->post->post_type != $this->post_type ) {
			return false;
		}

		$this->author          = get_userdata( $this->post->post_author );
		$this->publishing_user = get_userdata( get_current_user_id() );

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

		// Publishing user.
		$this->add_merge_tag( new MergeTag\User\UserID( array(
			'slug'          => $this->post_type . '_publishing_user_ID',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user ID' ), $post_name ),
			'property_name' => 'publishing_user',
		) ) );

    	$this->add_merge_tag( new MergeTag\User\UserLogin( array(
			'slug'          => $this->post_type . '_publishing_user_login',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user login'  ), $post_name ),
			'property_name' => 'publishing_user',
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserEmail( array(
			'slug'          => $this->post_type . '_publishing_user_email',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user email' ), $post_name ),
			'property_name' => 'publishing_user',
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( array(
			'slug'          => $this->post_type . '_publishing_user_nicename',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user nicename' ), $post_name ),
			'property_name' => 'publishing_user',
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserFirstName( array(
			'slug'          => $this->post_type . '_publishing_user_firstname',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user first name' ), $post_name ),
			'property_name' => 'publishing_user',
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( array(
			'slug'          => $this->post_type . '_publishing_user_lastname',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user last name' ), $post_name ),
			'property_name' => 'publishing_user',
		) ) );

    }

}
