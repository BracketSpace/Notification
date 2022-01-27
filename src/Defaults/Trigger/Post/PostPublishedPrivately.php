<?php
/**
 * Post published privately trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post published privately trigger class
 */
class PostPublishedPrivately extends PostTrigger {

	/**
	 * Status name of published post
	 *
	 * @var string
	 */
	protected static $publish_status = 'private';

	/**
	 * Post publishing user object
	 *
	 * @var \WP_User|false
	 */
	public $publishing_user;

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $post_type = 'post' ) {
		parent::__construct( [
			'post_type' => $post_type,
			'slug'      => 'post/' . $post_type . '/published-privately',
		] );

		$this->add_action( 'transition_post_status', 10, 3 );
	}

	/**
	 * Lazy loads the name
	 *
	 * @return string name
	 */
	public function get_name() : string {
		// translators: singular post name.
		return sprintf( __( '%s published privately', 'notification' ), WpObjectHelper::get_post_type_name( $this->post_type ) );
	}

	/**
	 * Lazy loads the description
	 *
	 * @return string description
	 */
	public function get_description() : string {
		return sprintf(
			// translators: 1. singular post name, 2. post type slug.
			__( 'Fires when %1$s (%2$s) is published privately', 'notification' ),
			WpObjectHelper::get_post_type_name( $this->post_type ),
			$this->post_type
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string   $new_status New post status.
	 * @param string   $old_status Old post status.
	 * @param \WP_Post $post       Post object.
	 * @return false|void
	 */
	public function context( $new_status, $old_status, $post ) {

		if ( $post->post_type !== $this->post_type ) {
			return false;
		}

		if ( self::$publish_status === $old_status || self::$publish_status !== $new_status ) {
			return false;
		}

		$this->{ $this->post_type } = $post;

		$this->author          = get_userdata( (int) $this->{ $this->post_type }->post_author );
		$this->last_editor     = get_userdata( (int) get_post_meta( $this->{ $this->post_type }->ID, '_edit_last', true ) );
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

		$post_type_name = WpObjectHelper::get_post_type_name( $this->post_type );

		parent::merge_tags();

		// Publishing user.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'          => sprintf( '%s_publishing_user_ID', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user ID', 'notification' ), $post_type_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'          => sprintf( '%s_publishing_user_login', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user login', 'notification' ), $post_type_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'          => sprintf( '%s_publishing_user_email', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user email', 'notification' ), $post_type_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'          => sprintf( '%s_publishing_user_nicename', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user nicename', 'notification' ), $post_type_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'          => sprintf( '%s_publishing_user_display_name', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user display name', 'notification' ), $post_type_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'          => sprintf( '%s_publishing_user_firstname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user first name', 'notification' ), $post_type_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'          => sprintf( '%s_publishing_user_lastname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user last name', 'notification' ), $post_type_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\Avatar( [
			'slug'          => sprintf( '%s_publishing_user_avatar', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user avatar', 'notification' ), $post_type_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserRole( [
			'slug'          => sprintf( '%s_publishing_user_role', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s publishing user role', 'notification' ), $post_type_name ),
			'property_name' => 'publishing_user',
			'group'         => __( 'Publishing user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => sprintf( '%s_publication_datetime', $this->post_type ),
			// translators: singular post name.
			'name' => sprintf( __( '%s publication date and time', 'notification' ), $post_type_name ),
		] ) );

	}

}
