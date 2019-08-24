<?php
/**
 * Post trashed trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Post trashed trigger class
 */
class PostTrashed extends PostTrigger {

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $post_type = 'post' ) {

		parent::__construct( [
			'post_type' => $post_type,
			'slug'      => 'wordpress/' . $post_type . '/trashed',
			// translators: singular post name.
			'name'      => sprintf( __( '%s trashed', 'notification' ), parent::get_post_type_name( $post_type ) ),
		] );

		$this->add_action( 'trash_' . $post_type, 10, 2 );

		// translators: 1. singular post name, 2. post type slug.
		$this->set_description( sprintf( __( 'Fires when %1$s (%2$s) is moved to trash', 'notification' ), parent::get_post_type_name( $post_type ), $post_type ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @param integer $post_id Post ID.
	 * @param object  $post    Post object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $post_id, $post ) {

		if ( $post->post_type !== $this->post_type ) {
			return false;
		}

		$this->{ $this->post_type } = $post;

		$this->author        = get_userdata( $this->{ $this->post_type }->post_author );
		$this->trashing_user = get_userdata( get_current_user_id() );

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

		// Trashing user.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'          => $this->post_type . '_trashing_user_ID',
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user ID', 'notification' ), $post_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'          => $this->post_type . '_trashing_user_login',
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user login', 'notification' ), $post_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'          => $this->post_type . '_trashing_user_email',
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user email', 'notification' ), $post_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'          => $this->post_type . '_trashing_user_nicename',
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user nicename', 'notification' ), $post_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'          => $this->post_type . '_trashing_user_display_name',
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user display name', 'notification' ), $post_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'          => $this->post_type . '_trashing_user_firstname',
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user first name', 'notification' ), $post_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'          => $this->post_type . '_trashing_user_lastname',
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user last name', 'notification' ), $post_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

	}

}
