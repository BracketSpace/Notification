<?php
/**
 * Post approved trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post approved trigger class. Approved means published after review.
 */
class PostApproved extends PostTrigger {

	/**
	 * Post approving user object
	 *
	 * @var \WP_User
	 */
	public $approving_user;

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $post_type = 'post' ) {
		parent::__construct( [
			'post_type' => $post_type,
			'slug'      => 'post/' . $post_type . '/approved',
		] );

		$this->add_action( 'pending_to_publish', 10 );
	}

	/**
	 * Lazy loads the name
	 *
	 * @return string name
	 */
	public function get_name() : string {
		// translators: singular post name.
		return sprintf( __( '%s approved', 'notification' ), WpObjectHelper::get_post_type_name( $this->post_type ) );
	}

	/**
	 * Lazy loads the description
	 *
	 * @return string description
	 */
	public function get_description() : string {
		return sprintf(
			// translators: 1. singular post name, 2. post type slug.
			__( 'Fires when %1$s (%2$s) is approved', 'notification' ),
			WpObjectHelper::get_post_type_name( $this->post_type ),
			$this->post_type
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param object $post Post object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $post ) {

		if ( $post->post_type !== $this->post_type ) {
			return false;
		}

		$this->{ $this->post_type } = $post;

		$this->author         = get_userdata( $this->{ $this->post_type }->post_author );
		$this->last_editor    = get_userdata( get_post_meta( $this->{ $this->post_type }->ID, '_edit_last', true ) );
		$this->approving_user = get_userdata( get_current_user_id() );

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

		// Approving user.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'          => sprintf( '%s_approving_user_ID', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s approving user ID', 'notification' ), $post_type_name ),
			'property_name' => 'approving_user',
			'group'         => __( 'Approving user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'          => sprintf( '%s_approving_user_login', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s approving user login', 'notification' ), $post_type_name ),
			'property_name' => 'approving_user',
			'group'         => __( 'Approving user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'          => sprintf( '%s_approving_user_email', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s approving user email', 'notification' ), $post_type_name ),
			'property_name' => 'approving_user',
			'group'         => __( 'Approving user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'          => sprintf( '%s_approving_user_nicename', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s approving user nicename', 'notification' ), $post_type_name ),
			'property_name' => 'approving_user',
			'group'         => __( 'Approving user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'          => sprintf( '%s_approving_user_display_name', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s approving user display name', 'notification' ), $post_type_name ),
			'property_name' => 'approving_user',
			'group'         => __( 'Approving user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'          => sprintf( '%s_approving_user_firstname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s approving user first name', 'notification' ), $post_type_name ),
			'property_name' => 'approving_user',
			'group'         => __( 'Approving user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'          => sprintf( '%s_approving_user_lastname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s approving user last name', 'notification' ), $post_type_name ),
			'property_name' => 'approving_user',
			'group'         => __( 'Approving user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\Avatar( [
			'slug'          => sprintf( '%s_approving_user_avatar', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s approving user avatar', 'notification' ), $post_type_name ),
			'property_name' => 'approving_user',
			'group'         => __( 'Approving user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserRole( [
			'slug'          => sprintf( '%s_approving_user_role', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s approving user role', 'notification' ), $post_type_name ),
			'property_name' => 'approving_user',
			'group'         => __( 'Approving user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => sprintf( '%s_approving_datetime', $this->post_type ),
			// translators: singular post name.
			'name' => sprintf( __( '%s approving date and time', 'notification' ), $post_type_name ),
		] ) );

	}

}
