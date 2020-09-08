<?php
/**
 * Comment trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Defaults\MergeTag;
use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Traits;

/**
 * Comment trigger class
 */
abstract class CommentTrigger extends Abstracts\Trigger {

	use Traits\Cache;

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

		$this->set_group( $this->get_current_comment_type_name() );

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

		$this->post_creation_datetime     = strtotime( $this->post->post_date_gmt );
		$this->post_modification_datetime = strtotime( $this->post->post_modified_gmt );
		$this->comment_datetime           = strtotime( $this->comment->comment_date_gmt );

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

		$this->add_merge_tag( new MergeTag\Comment\CommentID( [
			'comment_type' => $this->get_comment_type(),
		] ) );

		$this->add_merge_tag( new MergeTag\Comment\CommentContent( [
			'comment_type' => $this->get_comment_type(),
		] ) );

		$this->add_merge_tag( new MergeTag\Comment\CommentContentHtml( [
			'comment_type' => $this->get_comment_type(),
		] ) );

		$this->add_merge_tag( new MergeTag\Comment\CommentStatus( [
			'comment_type' => $this->get_comment_type(),
		] ) );

		$this->add_merge_tag( new MergeTag\Comment\CommentType( [
			'comment_type' => $this->get_comment_type(),
		] ) );

		$this->add_merge_tag( new MergeTag\Comment\CommentIsReply( [
			'comment_type' => $this->get_comment_type(),
		] ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug'  => 'comment_datetime',
			// Translators: Comment type name.
			'name'  => sprintf( __( '%s date and time', 'notification' ), self::get_current_comment_type_name() ),
			'group' => __( $this->get_current_comment_type_name(), 'notification' ),
		] ) );

		// Author.
		$this->add_merge_tag( new MergeTag\Comment\CommentAuthorIP( [
			'comment_type' => $this->get_comment_type(),
		] ) );

		$this->add_merge_tag( new MergeTag\Comment\CommentAuthorUserAgent( [
			'comment_type' => $this->get_comment_type(),
		] ) );

		$this->add_merge_tag( new MergeTag\Comment\CommentAuthorUrl( [
			'comment_type' => $this->get_comment_type(),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'  => 'comment_author_user_ID',
			// Translators: Comment type name.
			'name'  => sprintf( __( '%s author user ID', 'notification' ), self::get_current_comment_type_name() ),
			// Translators: comment type author.
			'group' => sprintf( __( '%s author', 'notification' ), $this->get_current_comment_type_name() ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'  => 'comment_author_user_email',
			// Translators: Comment type name.
			'name'  => sprintf( __( '%s author user email', 'notification' ), self::get_current_comment_type_name() ),
			// Translators: comment type author.
			'group' => sprintf( __( '%s author', 'notification' ), $this->get_current_comment_type_name() ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'  => 'comment_author_user_display_name',
			// Translators: Comment type name.
			'name'  => sprintf( __( '%s author user display name', 'notification' ), self::get_current_comment_type_name() ),
			// Translators: comment type author.
			'group' => sprintf( __( '%s author', 'notification' ), $this->get_current_comment_type_name() ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\Avatar( [
			'slug'  => 'comment_author_user_avatar',
			// Translators: Comment type name.
			'name'  => sprintf( __( '%s author user avatar', 'notification' ), self::get_current_comment_type_name() ),
			// Translators: comment type author.
			'group' => sprintf( __( '%s author', 'notification' ), $this->get_current_comment_type_name() ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\AvatarUrl( [
			'slug'  => 'comment_author_user_avatar_url',
			// Translators: Comment type name.
			'name'  => sprintf( __( '%s author user avatar url', 'notification' ), self::get_current_comment_type_name() ),
			// Translators: comment type author.
			'group' => sprintf( __( '%s author', 'notification' ), $this->get_current_comment_type_name() ),
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
			// Translators: singular post name.
			'name'  => sprintf( __( '%s creation date and time', 'notification' ), __( 'Post', 'notification' ) ),
			'group' => self::get_post_type_name( 'post' ),
		] ) );

		$this->add_merge_tag( new MergeTag\DateTime\DateTime( [
			'slug'  => 'post_modification_datetime',
			// Translators: singular post name.
			'name'  => sprintf( __( '%s modification date and time', 'notification' ), __( 'Post', 'notification' ) ),
			'group' => self::get_post_type_name( 'post' ),
		] ) );

		// Post Author.
		$this->add_merge_tag( new MergeTag\User\UserID( [
			'slug'          => 'post_author_user_ID',
			// Translators: singular post name.
			'name'          => sprintf( __( '%s author user ID', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			// Translators: Post type name.
			'group'         => sprintf( __( '%s author', 'notification' ), self::get_post_type_name( 'post' ) ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLogin( [
			'slug'          => 'post_author_user_login',
			// Translators: singular post name.
			'name'          => sprintf( __( '%s author user login', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			// Translators: Post type name.
			'group'         => sprintf( __( '%s author', 'notification' ), self::get_post_type_name( 'post' ) ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserEmail( [
			'slug'          => 'post_author_user_email',
			// Translators: singular post name.
			'name'          => sprintf( __( '%s author user email', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			// Translators: Post type name.
			'group'         => sprintf( __( '%s author', 'notification' ), self::get_post_type_name( 'post' ) ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserNicename( [
			'slug'          => 'post_author_user_nicename',
			// Translators: singular post name.
			'name'          => sprintf( __( '%s author user nicename', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			// Translators: Post type name.
			'group'         => sprintf( __( '%s author', 'notification' ), self::get_post_type_name( 'post' ) ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( [
			'slug'          => 'post_author_user_display_name',
			// Translators: singular post name.
			'name'          => sprintf( __( '%s author user display name', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			// Translators: Post type name.
			'group'         => sprintf( __( '%s author', 'notification' ), self::get_post_type_name( 'post' ) ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserFirstName( [
			'slug'          => 'post_author_user_firstname',
			// Translators: singular post name.
			'name'          => sprintf( __( '%s author user first name', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			// Translators: Post type name.
			'group'         => sprintf( __( '%s author', 'notification' ), self::get_post_type_name( 'post' ) ),
		] ) );

		$this->add_merge_tag( new MergeTag\User\UserLastName( [
			'slug'          => 'post_author_user_lastname',
			// Translators: singular post name.
			'name'          => sprintf( __( '%s author user last name', 'notification' ), __( 'Post', 'notification' ) ),
			'property_name' => 'post_author',
			// Translators: Post type name.
			'group'         => sprintf( __( '%s author', 'notification' ), self::get_post_type_name( 'post' ) ),
		] ) );

	}

}
