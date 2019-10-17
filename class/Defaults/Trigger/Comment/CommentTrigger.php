<?php
/**
 * Comment trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;

/**
 * Comment trigger class
 */
abstract class CommentTrigger extends Abstracts\Trigger {

	/**
	 * Comment Type slug
	 *
	 * @var string
	 */
	protected $comment_type;

	/**
	 * Constructor
	 *
	 * @param array $params trigger configuration params.
	 */
	public function __construct( $params = [] ) {

		if ( ! isset( $params['comment_type'], $params['slug'], $params['name'] ) ) {
			trigger_error( 'CommentTrigger requires comment_type, slug and name params.', E_USER_ERROR );
		}

		$this->comment_type = $params['comment_type'];

		parent::__construct( $params['slug'], $params['name'] );

		$this->set_group( __( ucfirst( $this->comment_type ), 'notification' ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @return void
	 */
	public function assign_properties() {

		$this->user_object               = new \StdClass();
		$this->user_object->ID           = ( $this->comment->user_id ) ? $this->comment->user_id : 0;
		$this->user_object->display_name = $this->comment->comment_author;
		$this->user_object->user_email   = $this->comment->comment_author_email;

		$this->post      = get_post( $this->comment->comment_post_ID );
		$this->post_type = $this->post->post_type;

		$this->post_creation_datetime     = strtotime( $this->post->post_date );
		$this->post_modification_datetime = strtotime( $this->post->post_modified );
		$this->comment_datetime           = strtotime( $this->comment->date );

		$this->post_author = get_userdata( $this->post->post_author );

	}

	/**
	 * Check if comment is correct type
	 *
	 * @param mixed $comment Comment object or Comment ID.
	 * @return boolean
	 */
	public function is_correct_type( $comment ) {
		return get_comment_type( $comment ) === $this->comment_type;
	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\Comment\CommentID() );
		$this->add_merge_tag( new MergeTag\Comment\CommentContent() );
		$this->add_merge_tag( new MergeTag\Comment\CommentContentHtml() );
		$this->add_merge_tag( new MergeTag\Comment\CommentStatus() );
		$this->add_merge_tag( new MergeTag\Comment\CommentType() );
		$this->add_merge_tag( new MergeTag\Comment\CommentIsReply() );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug'  => 'comment_datetime',
			'name'  => __( 'Comment date and time', 'notification' ),
			'group' => __( ucfirst( $this->comment_type ), 'notification' ),
		] ) );

		// Author.
		$this->add_merge_tag( new MergeTag\Comment\CommentAuthorIP() );
		$this->add_merge_tag( new MergeTag\Comment\CommentAuthorUserAgent() );
		$this->add_merge_tag( new MergeTag\Comment\CommentAuthorUrl() );

		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'  => 'comment_author_user_ID',
			'name'  => __( 'Comment author user ID', 'notification' ),
			// translators: comment type author.
			'group' => sprintf( __( '%s author', 'notification' ), ucfirst( $this->comment_type ) ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'  => 'comment_author_user_email',
			'name'  => __( 'Comment author user email', 'notification' ),
			// translators: comment type author.
			'group' => sprintf( __( '%s author', 'notification' ), ucfirst( $this->comment_type ) ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'  => 'comment_author_user_display_name',
			'name'  => __( 'Comment author user display name', 'notification' ),
			// translators: comment type author.
			'group' => sprintf( __( '%s author', 'notification' ), ucfirst( $this->comment_type ) ),
		] ) );

		// Post.
		$this->add_merge_tag( new MergeTag\Post\PostID() );
		$this->add_merge_tag( new MergeTag\Post\PostPermalink() );
		$this->add_merge_tag( new MergeTag\Post\PostTitle() );
		$this->add_merge_tag( new MergeTag\Post\PostSlug() );
		$this->add_merge_tag( new MergeTag\Post\PostContent() );
		$this->add_merge_tag( new MergeTag\Post\PostExcerpt() );
		$this->add_merge_tag( new MergeTag\Post\PostStatus() );
		$this->add_merge_tag( new MergeTag\Post\PostType() );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug'  => 'post_creation_datetime',
			// translators: singular post name.
			'name'  => sprintf( __( '%s creation date and time', 'notification' ), __( 'Post', 'notification' ) ),
			'group' => $this->get_post_type_nicename(),
		] ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug'  => 'post_modification_datetime',
			// translators: singular post name.
			'name'  => sprintf( __( '%s modification date and time', 'notification' ), __( 'Post', 'notification' ) ),
			'group' => $this->get_post_type_nicename(),
		] ) );

		// Post Author.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'          => 'post_author_user_ID',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user ID', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			'group'         => $this->get_post_type_nicename(),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'          => 'post_author_user_login',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user login', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			'group'         => $this->get_post_type_nicename(),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'          => 'post_author_user_email',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user email', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			'group'         => $this->get_post_type_nicename(),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'          => 'post_author_user_nicename',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user nicename', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			'group'         => $this->get_post_type_nicename(),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'          => 'post_author_user_display_name',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user display name', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			'group'         => $this->get_post_type_nicename(),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'          => 'post_author_user_firstname',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user first name', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			'group'         => $this->get_post_type_nicename(),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'          => 'post_author_user_lastname',
			// translators: singular post name.
			'name'          => sprintf( __( '%s author user last name', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			'group'         => $this->get_post_type_nicename(),
		] ) );

	}

	/**
	 * Gets nice, translated post type name
	 *
	 * @since  6.0.0
	 * @return string post name
	 */
	public function get_post_type_nicename() {

		$post_type = get_post_type_object( 'post' );

		if ( empty( $post_type ) ) {
			return '';
		}

		return $post_type->labels->singular_name;

	}

}
