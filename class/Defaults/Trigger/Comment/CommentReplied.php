<?php
/**
 * Comment replied trigger
 *
 * @package notification
 */

namespace BracketSpace\Notification\Defaults\Trigger\Comment;

use BracketSpace\Notification\Defaults\MergeTag;

/**
 * Comment replied trigger class
 */
class CommentReplied extends CommentTrigger {

	/**
	 * Constructor
	 *
	 * @param string $comment_type optional, default: comment.
	 */
	public function __construct( $comment_type = 'comment' ) {

		parent::__construct(
			array(
				'slug'         => 'wordpress/comment_' . $comment_type . '_replied',
				// Translators: %s comment type.
				'name'         => sprintf( __( '%s replied', 'notification' ), ucfirst( $comment_type ) ),
				'comment_type' => $comment_type,
			)
		);

		$this->add_action( 'transition_comment_status', 10, 3 );
		$this->add_action( 'notification_insert_comment_proxy', 10, 3 );

		// translators: comment type.
		$this->set_description( sprintf( __( 'Fires when %s is replied and the reply is approved', 'notification' ), __( ucfirst( $comment_type ), 'notification' ) ) );

	}

	/**
	 * Assigns action callback args to object
	 *
	 * @param string $comment_new_status New comment status.
	 * @param string $comment_old_status Old comment status.
	 * @param object $comment            Comment object.
	 * @return mixed void or false if no notifications should be sent
	 */
	public function action( $comment_new_status, $comment_old_status, $comment ) {

		$this->comment = $comment;

		if ( 'spam' === $this->comment->comment_approved && notification_get_setting( 'triggers/comment/akismet' ) ) {
			return false;
		}

		if ( $comment_new_status === $comment_old_status || 'approved' !== $comment_new_status ) {
			return false;
		}

		// Bail if comment is not a reply.
		if ( empty( $this->comment->comment_parent ) ) {
			return false;
		}

		if ( ! $this->is_correct_type( $this->comment ) ) {
			return false;
		}

		$this->parent_comment = get_comment( $this->comment->comment_parent );

		$this->parent_comment_user_object               = new \StdClass();
		$this->parent_comment_user_object->ID           = ( $this->parent_comment->user_id ) ? $this->parent_comment->user_id : 0;
		$this->parent_comment_user_object->display_name = $this->parent_comment->comment_author;
		$this->parent_comment_user_object->user_email   = $this->parent_comment->comment_author_email;

		parent::assign_properties();

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		parent::merge_tags();

		$this->add_merge_tag( new MergeTag\Comment\CommentActionApprove() );
		$this->add_merge_tag( new MergeTag\Comment\CommentActionTrash() );
		$this->add_merge_tag( new MergeTag\Comment\CommentActionDelete() );
		$this->add_merge_tag( new MergeTag\Comment\CommentActionSpam() );

		// Parent comment.
		$this->add_merge_tag(
			new MergeTag\Comment\CommentID(
				array(
					'slug'          => 'parent_comment_ID',
					'name'          => __( 'Parent comment ID', 'notification' ),
					'property_name' => 'parent_comment',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentContent(
				array(
					'slug'          => 'parent_comment_content',
					'name'          => __( 'Parent comment content', 'notification' ),
					'property_name' => 'parent_comment',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentStatus(
				array(
					'slug'          => 'parent_comment_status',
					'name'          => __( 'Parent comment status', 'notification' ),
					'property_name' => 'parent_comment',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentType(
				array(
					'slug'          => 'parent_comment_type',
					'name'          => __( 'Parent comment type', 'notification' ),
					'property_name' => 'parent_comment',
				)
			)
		);

		// Parent comment author.
		$this->add_merge_tag(
			new MergeTag\Comment\CommentAuthorIP(
				array(
					'slug'          => 'parent_comment_author_IP',
					'name'          => __( 'Parent comment author IP', 'notification' ),
					'property_name' => 'parent_comment',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentAuthorUserAgent(
				array(
					'slug'          => 'parent_comment_user_agent',
					'name'          => __( 'Parent comment user agent', 'notification' ),
					'property_name' => 'parent_comment',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\Comment\CommentAuthorUrl(
				array(
					'slug'          => 'parent_comment_author_url',
					'name'          => __( 'Parent comment author URL', 'notification' ),
					'property_name' => 'parent_comment',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserID(
				array(
					'slug'          => 'parent_comment_author_user_ID',
					'name'          => __( 'Parent comment author user ID', 'notification' ),
					'property_name' => 'parent_comment_user_object',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserEmail(
				array(
					'slug'          => 'parent_comment_author_user_email',
					'name'          => __( 'Parent comment author user email', 'notification' ),
					'property_name' => 'parent_comment_user_object',
				)
			)
		);

		$this->add_merge_tag(
			new MergeTag\User\UserDisplayName(
				array(
					'slug'          => 'parent_comment_author_user_display_name',
					'name'          => __( 'Parent comment author user display name', 'notification' ),
					'property_name' => 'parent_comment_user_object',
				)
			)
		);

	}

}
