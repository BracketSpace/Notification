<?php
/**
 * Post updated trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post updated trigger class
 */
class PostUpdated extends PostTrigger {

	/**
	 * Post updating user object
	 *
	 * @var \WP_User
	 */
	public $updating_user;

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $post_type = 'post' ) {
		parent::__construct( [
			'post_type' => $post_type,
			'slug'      => 'post/' . $post_type . '/updated',
		] );

		$this->add_action( 'post_updated', 10, 3 );
	}

	/**
	 * Lazy loads the name
	 *
	 * @return string name
	 */
	public function get_name() : string {
		// translators: singular post name.
		return sprintf( __( '%s updated', 'notification' ), WpObjectHelper::get_post_type_name( $this->post_type ) );
	}

	/**
	 * Lazy loads the description
	 *
	 * @return string description
	 */
	public function get_description() : string {
		return sprintf(
			// translators: 1. singular post name, 2. post type slug.
			__( 'Fires when %1$s (%2$s) is updated', 'notification' ),
			WpObjectHelper::get_post_type_name( $this->post_type ),
			$this->post_type
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param integer  $post_id     Post ID.
	 * @param \WP_Post $post        Post object.
	 * @param \WP_Post $post_before Post before object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $post_id, $post, $post_before ) {

		if ( $post->post_type !== $this->post_type ) {
			return false;
		}

		// Filter the post statuses for which the notification should be sent. By default it will be send only if you update already published post.
		$updated_post_statuses = apply_filters( 'notification/trigger/wordpress/post/updated/statuses', [ 'publish' ], $this->post_type );

		// Pending posts doesn't have the slug, otherwise we should bail.
		if ( 'pending' !== $post->post_status && empty( $post->post_name ) ) {
			return false;
		}

		if ( ! in_array( $post_before->post_status, $updated_post_statuses, true ) || 'trash' === $post->post_status ) {
			return false;
		}

		$this->{ $this->post_type } = $post;

		$updating_user_id = get_current_user_id();

		$this->author        = get_userdata( (int) $this->{ $this->post_type }->post_author );
		$this->last_editor   = get_userdata( get_post_meta( $this->{ $this->post_type }->ID, '_edit_last', true ) );
		$this->updating_user = get_userdata( $updating_user_id );

		$this->{ $this->post_type . '_creation_datetime' }     = strtotime( $this->{ $this->post_type }->post_date_gmt );
		$this->{ $this->post_type . '_modification_datetime' } = strtotime( $this->{ $this->post_type }->post_modified_gmt );

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$post_type_name = WpObjectHelper::get_post_type_name( $this->post_type );

		parent::merge_tags();

		// updating user.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'          => sprintf( '%s_updating_user_ID', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user ID', 'notification' ), $post_type_name ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'          => sprintf( '%s_updating_user_login', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user login', 'notification' ), $post_type_name ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'          => sprintf( '%s_updating_user_email', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user email', 'notification' ), $post_type_name ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'          => sprintf( '%s_updating_user_nicename', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user nicename', 'notification' ), $post_type_name ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'          => sprintf( '%s_updating_user_display_name', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user display name', 'notification' ), $post_type_name ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'          => sprintf( '%s_updating_user_firstname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user first name', 'notification' ), $post_type_name ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'          => sprintf( '%s_updating_user_lastname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user last name', 'notification' ), $post_type_name ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\Avatar( [
			'slug'          => sprintf( '%s_updating_user_avatar', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user email', 'notification' ), $post_type_name ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserRole( [
			'slug'          => sprintf( '%s_updating_user_role', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s updating user role', 'notification' ), $post_type_name ),
			'property_name' => 'updating_user',
			'group'         => __( 'Updating user', 'notification' ),
		] ) );

		// add revision link tag if revisions are enabled.
		if ( defined( 'WP_POST_REVISIONS' ) && WP_POST_REVISIONS ) {
			$this->add_merge_tag( new MergeTag\Post\RevisionLink() );
		}
	}

}
