<?php
/**
 * Post sent for reviewscheduled trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post sent for review trigger class
 */
class PostScheduled extends PostTrigger {

	/**
	 * Post scheduling user object
	 *
	 * @var \WP_User
	 */
	public $scheduling_user;

	/**
	 * Constructor
	 *
	 * @param string $post_type optional, default: post.
	 */
	public function __construct( $post_type = 'post' ) {
		parent::__construct( [
			'post_type' => $post_type,
			'slug'      => 'post/' . $post_type . '/scheduled',
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
		return sprintf( __( '%s scheduled', 'notification' ), WpObjectHelper::get_post_type_name( $this->post_type ) );
	}

	/**
	 * Lazy loads the description
	 *
	 * @return string description
	 */
	public function get_description() : string {
		return sprintf(
			// translators: 1. singular post name, 2. post type slug.
			__( 'Fires when %1$s (%2$s) is scheduled', 'notification' ),
			WpObjectHelper::get_post_type_name( $this->post_type ),
			$this->post_type
		);
	}

	/**
	 * Sets trigger's context
	 *
	 * @param string $new_status New post status.
	 * @param string $old_status Old post status.
	 * @param object $post       Post object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function context( $new_status, $old_status, $post ) {

		if ( $post->post_type !== $this->post_type ) {
			return false;
		}

		if ( 'future' === $old_status || 'future' !== $new_status ) {
			return false;
		}

		$this->{ $this->post_type } = $post;

		$scheduling_user_id = get_current_user_id();

		$this->author          = get_userdata( $this->{ $this->post_type }->post_author );
		$this->last_editor     = get_userdata( get_post_meta( $this->{ $this->post_type }->ID, '_edit_last', true ) );
		$this->scheduling_user = get_userdata( $scheduling_user_id );

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

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => sprintf( '%s_publication_datetime', $this->post_type ),
			// translators: singular post name.
			'name' => sprintf( __( '%s publication date and time', 'notification' ), $post_type_name ),
		] ) );

		// Scheduling user.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'          => sprintf( '%s_scheduling_user_ID', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s scheduling user ID', 'notification' ), $post_type_name ),
			'property_name' => 'scheduling_user',
			'group'         => __( 'Scheduling user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'          => sprintf( '%s_scheduling_user_login', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s scheduling user login', 'notification' ), $post_type_name ),
			'property_name' => 'scheduling_user',
			'group'         => __( 'Scheduling user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'          => sprintf( '%s_scheduling_user_email', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s scheduling user email', 'notification' ), $post_type_name ),
			'property_name' => 'scheduling_user',
			'group'         => __( 'Scheduling user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'          => sprintf( '%s_scheduling_user_nicename', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s scheduling user nicename', 'notification' ), $post_type_name ),
			'property_name' => 'scheduling_user',
			'group'         => __( 'Scheduling user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'          => sprintf( '%s_scheduling_user_display_name', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s scheduling user display name', 'notification' ), $post_type_name ),
			'property_name' => 'scheduling_user',
			'group'         => __( 'Scheduling user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'          => sprintf( '%s_scheduling_user_firstname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s scheduling user first name', 'notification' ), $post_type_name ),
			'property_name' => 'scheduling_user',
			'group'         => __( 'Scheduling user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'          => sprintf( '%s_scheduling_user_lastname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s scheduling user last name', 'notification' ), $post_type_name ),
			'property_name' => 'scheduling_user',
			'group'         => __( 'Scheduling user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\Avatar( [
			'slug'          => sprintf( '%s_scheduling_user_avatar', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s scheduling user email', 'notification' ), $post_type_name ),
			'property_name' => 'scheduling_user',
			'group'         => __( 'Scheduling user', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserRole( [
			'slug'          => sprintf( '%s_scheduling_user_role', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s scheduling user role', 'notification' ), $post_type_name ),
			'property_name' => 'scheduling_user',
			'group'         => __( 'Scheduling user', 'notification' ),
		] ) );

	}

}
