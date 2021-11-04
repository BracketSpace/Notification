<?php
/**
 * Post trigger abstract
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Post;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Post trigger class
 */
abstract class PostTrigger extends Abstracts\Trigger {

	/**
	 * Post Type slug
	 *
	 * @var string
	 */
	public $post_type;

	/**
	 * Post author user object
	 *
	 * @var \WP_User
	 */
	public $author;

	/**
	 * Post last editor user object
	 *
	 * @var \WP_User
	 */
	public $last_editor;

	/**
	 * Constructor
	 *
	 * @param array $params trigger configuration params.
	 */
	public function __construct( $params = [] ) {
		if ( ! isset( $params['post_type'], $params['slug'] ) ) {
			trigger_error( 'PostTrigger requires post_type and slug params.', E_USER_ERROR );
		}

		$this->post_type = $params['post_type'];

		parent::__construct( $params['slug'] );
	}

	/**
	 * Lazy loads group name
	 *
	 * @return string|null Group name
	 */
	public function get_group() {
		return WpObjectHelper::get_post_type_name( $this->post_type );
	}

	/**
	 * Gets Post Type slug
	 *
	 * @return string Post Type slug
	 */
	public function get_post_type() : string {
		return $this->post_type;
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$post_type_name = WpObjectHelper::get_post_type_name( $this->post_type );

		$this->add_merge_tag( new MergeTag\Post\PostID( [
			'post_type' => $this->post_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Post\PostPermalink( [
			'post_type' => $this->post_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Post\PostTitle( [
			'post_type' => $this->post_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Post\PostSlug( [
			'post_type' => $this->post_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Post\PostContent( [
			'post_type' => $this->post_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Post\PostContentHtml( [
			'post_type' => $this->post_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Post\PostExcerpt( [
			'post_type' => $this->post_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Post\PostStatus( [
			'post_type' => $this->post_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Post\ThumbnailUrl( [
			'post_type' => $this->post_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Post\FeaturedImageUrl( [
			'post_type' => $this->post_type,
		] ) );

		$this->add_merge_tag( new MergeTag\Post\FeaturedImageId( [
			'post_type' => $this->post_type,
		] ) );

		if ( 'post' === $this->post_type ) {
			$this->add_merge_tag( new MergeTag\StringTag( [
				'slug'     => sprintf( '%s_sticky', $this->post_type ),
				// translators: singular post name.
				'name'     => sprintf( __( '%s sticky status', 'notification' ), $post_type_name ),
				'group'    => $post_type_name,
				'resolver' => function ( $trigger ) {
					return is_sticky( $trigger->{ $this->post_type }->ID ) ? __( 'Sticky', 'notification' ) : __( 'Not sticky', 'notification' );
				},
			] ) );
		}

		$taxonomies = get_object_taxonomies( $this->post_type, 'objects' );

		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				// Post format special treatment.
				if ( 'post_format' === $taxonomy->name ) {
					$group = $post_type_name;
				} else {
					$group = __( 'Taxonomies', 'notification' );
				}

				$this->add_merge_tag( new MergeTag\Post\PostTerms( [
					'post_type' => $this->post_type,
					'taxonomy'  => $taxonomy,
					'group'     => $group,
				] ) );
			}
		}

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => sprintf( '%s_creation_datetime', $this->post_type ),
			// translators: singular post name.
			'name' => sprintf( __( '%s creation date and time', 'notification' ), $post_type_name ),
		] ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug' => sprintf( '%s_modification_datetime', $this->post_type ),
			// translators: singular post name.
			'name' => sprintf( __( '%s modification date and time', 'notification' ), $post_type_name ),
		] ) );

		// Author.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'          => sprintf( '%s_author_user_ID', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user ID', 'notification' ), $post_type_name ),
			'property_name' => 'author',
			'group'         => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'          => sprintf( '%s_author_user_login', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user login', 'notification' ), $post_type_name ),
			'property_name' => 'author',
			'group'         => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'          => sprintf( '%s_author_user_email', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user email', 'notification' ), $post_type_name ),
			'property_name' => 'author',
			'group'         => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'          => sprintf( '%s_author_user_nicename', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user nicename', 'notification' ), $post_type_name ),
			'property_name' => 'author',
			'group'         => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'          => sprintf( '%s_author_user_display_name', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user display name', 'notification' ), $post_type_name ),
			'property_name' => 'author',
			'group'         => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'          => sprintf( '%s_author_user_firstname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user first name', 'notification' ), $post_type_name ),
			'property_name' => 'author',
			'group'         => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'          => sprintf( '%s_author_user_lastname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user last name', 'notification' ), $post_type_name ),
			'property_name' => 'author',
			'group'         => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\Avatar( [
			'slug'          => sprintf( '%s_author_user_avatar', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user avatar', 'notification' ), $post_type_name ),
			'property_name' => 'author',
			'group'         => __( 'Author', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserRole( [
			'slug'          => sprintf( '%s_author_user_role', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user role', 'notification' ), $post_type_name ),
			'property_name' => 'author',
			'group'         => __( 'Author', 'notification' ),
		] ) );

		// Last updated by.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'          => sprintf( '%s_last_editor_ID', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s last editor ID', 'notification' ), $post_type_name ),
			'property_name' => 'last_editor',
			'group'         => __( 'Last editor', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'          => sprintf( '%s_last_editor_login', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s last editor login', 'notification' ), $post_type_name ),
			'property_name' => 'last_editor',
			'group'         => __( 'Last editor', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'          => sprintf( '%s_last_editor_email', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s last editor email', 'notification' ), $post_type_name ),
			'property_name' => 'last_editor',
			'group'         => __( 'Last editor', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'          => sprintf( '%s_last_editor_nicename', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s last editor nicename', 'notification' ), $post_type_name ),
			'property_name' => 'last_editor',
			'group'         => __( 'Last editor', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'          => sprintf( '%s_last_editor_display_name', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s last editor display name', 'notification' ), $post_type_name ),
			'property_name' => 'last_editor',
			'group'         => __( 'Last editor', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'          => sprintf( '%s_last_editor_firstname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s last editor first name', 'notification' ), $post_type_name ),
			'property_name' => 'last_editor',
			'group'         => __( 'Last editor', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'          => sprintf( '%s_last_editor_lastname', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s last editor last name', 'notification' ), $post_type_name ),
			'property_name' => 'last_editor',
			'group'         => __( 'Last editor', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\Avatar( [
			'slug'          => sprintf( '%s_last_editor_avatar', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s last editor avatar', 'notification' ), $post_type_name ),
			'property_name' => 'last_editor',
			'group'         => __( 'Last editor', 'notification' ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserRole( [
			'slug'          => sprintf( '%s_last_editor_role', $this->post_type ),
			// translators: singular post name.
			'name'          => sprintf( __( '%s last editor role', 'notification' ), $post_type_name ),
			'property_name' => 'last_editor',
			'group'         => __( 'Last editor', 'notification' ),
		] ) );

	}

}
