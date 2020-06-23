<?php
/**
 * Post published trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;

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

		parent::__construct( [
			'post_type' => $post_type,
			'slug'      => 'post/' . $post_type . '/published',
			// translators: singular post name.
			'name'      => sprintf( __( '%s published', 'notification' ), parent::get_post_type_name( $post_type ) ),
		] );

		$this->add_action( 'new_to_publish', 10 );
		$this->add_action( 'auto-draft_to_publish', 10 );
		$this->add_action( 'draft_to_publish', 10 );
		$this->add_action( 'pending_to_publish', 10 );
		$this->add_action( 'private_to_publish', 10 );
		$this->add_action( 'future_to_publish', 10 );

		// translators: 1. singular post name, 2. post type slug.
		$this->set_description( sprintf( __( 'Fires when %1$s (%2$s) is published', 'notification' ), parent::get_post_type_name( $post_type ), $post_type ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @param object $post Post object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $post ) {

		if ( $post->post_type !== $this->post_type ) {
			return false;
		}

		$this->{ $this->post_type } = $post;

		$this->author          = get_userdata( $this->{ $this->post_type }->post_author );
		$this->last_editor     = get_userdata( get_post_meta( $this->{ $this->post_type }->ID, '_edit_last', true ) );
		$this->publishing_user = get_userdata( get_current_user_id() );

		$this->{ $this->post_type . '_creation_datetime' }     = strtotime( $this->{ $this->post_type }->post_date_gmt );
		$this->{ $this->post_type . '_publication_datetime' }  = strtotime( $this->{ $this->post_type }->post_date_gmt );
		$this->{ $this->post_type . '_modification_datetime' } = strtotime( $this->{ $this->post_type }->post_modified_gmt );

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
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'          => $this->post_type . '_publishing_user_ID',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user ID', 'notification' ), $post_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'          => $this->post_type . '_publishing_user_login',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user login', 'notification' ), $post_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'          => $this->post_type . '_publishing_user_email',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user email', 'notification' ), $post_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'          => $this->post_type . '_publishing_user_nicename',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user nicename', 'notification' ), $post_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'          => $this->post_type . '_publishing_user_display_name',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user display name', 'notification' ), $post_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'          => $this->post_type . '_publishing_user_firstname',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user first name', 'notification' ), $post_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'          => $this->post_type . '_publishing_user_lastname',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user last name', 'notification' ), $post_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\Avatar( [
			'slug'          => $this->post_type . '_publishing_user_avatar',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user avatar', 'notification' ), $post_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserRole( [
			'slug'          => $this->post_type . '_publishing_user_role',
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user role', 'notification' ), $post_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => $this->post_type . '_publication_datetime',
			// translators: singular post name.
			'name' => sprintf( __( '%s publication date and time', 'notification' ), $post_name ),
		] ) );

	}

}
