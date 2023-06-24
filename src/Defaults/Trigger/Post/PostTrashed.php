<?php
/**
 * Post trashed trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post trashed trigger class
 */
class PostTrashed extends PostTrigger {

	/**
	 * Post trashing user object
	 *
	 * @var \WP_User|false
	 */
	public $trashing_user;

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $post_type = 'post' ) {
		parent::__construct( [
			'post_type' => $post_type,
			'slug'      => 'post/' . $post_type . '/trashed',
		] );

		$this->add_action( 'trash_' . $post_type, 10, 2 );
	}

	/**
	 * Lazy loads the name
	 *
	 * @return string name
	 */
	public function get_name() : string {
		// translators: singular post name.
		return sprintf( __( '%s trashed', 'notification' ), WpObjectHelper::get_post_type_name( $this->post_type ) );
	}

	/**
	 * Lazy loads the description
	 *
	 * @return string description
	 */
	public function get_description() : string {
		return sprintf(
			// translators: 1. singular post name, 2. post type slug.
			__( 'Fires when %1$s (%2$s) is moved to trash', 'notification' ),
			WpObjectHelper::get_post_type_name( $this->post_type ),
			$this->post_type
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param integer $post_id Post ID.
	 * @param object  $post    Post object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $post_id, $post ) {

		if ( $post->post_type !== $this->post_type ) {
			return false;
		}

		$this->{ $this->post_type } = $post;

		$this->author        = get_userdata( (int) $this->{ $this->post_type }->post_author );
		$this->last_editor   = get_userdata( (int) get_post_meta( $this->{ $this->post_type }->ID, '_edit_last', true ) );
		$this->trashing_user = get_userdata( get_current_user_id() );

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

		// Trashing user.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'          => sprintf( '%s_trashing_user_ID', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user ID', 'notification' ), $post_type_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'          => sprintf( '%s_trashing_user_login', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user login', 'notification' ), $post_type_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'          => sprintf( '%s_trashing_user_email', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user email', 'notification' ), $post_type_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'          => sprintf( '%s_trashing_user_nicename', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user nicename', 'notification' ), $post_type_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'          => sprintf( '%s_trashing_user_display_name', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user display name', 'notification' ), $post_type_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'          => sprintf( '%s_trashing_user_firstname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user first name', 'notification' ), $post_type_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'          => sprintf( '%s_trashing_user_lastname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user last name', 'notification' ), $post_type_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\Avatar( [
			'slug'          => sprintf( '%s_trashing_user_avatar', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user avatar', 'notification' ), $post_type_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserRole( [
			'slug'          => sprintf( '%s_trashing_user_role', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s trashing user role', 'notification' ), $post_type_name ),
			'property_name' => 'trashing_user',
			'group'         => __( 'Trashing user', 'notification' ),
		] ) );

	}

}
