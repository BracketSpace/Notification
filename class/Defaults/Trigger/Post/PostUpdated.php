<?php
/**
 * Post updated trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Post updated trigger class
 */
class PostUpdated extends PostTrigger {

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $post_type = 'post' ) {

		parent::__construct( array(
			'post_type' => $post_type,
			'slug'      => 'wordpress/' . $post_type . '/updated',
			// translators: singular post name.
			'name'      => sprintf( __( '%s updated', 'notification' ), parent::get_post_type_name( $post_type ) ),
		) );

		$this->add_action( 'post_updated', 10, 3 );

		// translators: 1. singular post name, 2. post type slug.
		$this->set_description( sprintf( __( 'Fires when %s (%s) is updated', 'notification' ), parent::get_post_type_name( $post_type ), $post_type ) );

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
		// WP_Post object.
		$post_before = $this->callback_args[2];

		if ( $this->{ $this->post_type }->post_type != $this->post_type ) {
			return false;
		}

		if ( empty( $this->{ $this->post_type }->post_name ) || $post_before->post_status != 'publish'  || $this->{ $this->post_type }->post_status == 'trash' ) {
			return false;
		}

		$this->author        = get_userdata( $this->{ $this->post_type }->post_author );
		$this->updating_user = get_userdata( get_current_user_id() );

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

		// updating user.
		$this->add_merge_tag( new MergeTag\User\UserID( array(
			'slug'          => $this->post_type . '_updating_user_ID',
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user ID', 'notification' ), $post_name ),
			'property_name' => 'updating_user',
		) ) );

    	$this->add_merge_tag( new MergeTag\User\UserLogin( array(
			'slug'          => $this->post_type . '_updating_user_login',
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user login', 'notification' ), $post_name ),
			'property_name' => 'updating_user',
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserEmail( array(
			'slug'          => $this->post_type . '_updating_user_email',
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user email', 'notification' ), $post_name ),
			'property_name' => 'updating_user',
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( array(
			'slug'          => $this->post_type . '_updating_user_nicename',
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user nicename', 'notification' ), $post_name ),
			'property_name' => 'updating_user',
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( array(
			'slug'          => $this->post_type . '_updating_user_display_name',
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user display name', 'notification' ), $post_name ),
			'property_name' => 'updating_user',
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserFirstName( array(
			'slug'          => $this->post_type . '_updating_user_firstname',
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user first name', 'notification' ), $post_name ),
			'property_name' => 'updating_user',
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( array(
			'slug'          => $this->post_type . '_updating_user_lastname',
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user last name', 'notification' ), $post_name ),
			'property_name' => 'updating_user',
		) ) );

    }

}
