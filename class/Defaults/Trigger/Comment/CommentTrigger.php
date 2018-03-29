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
	public function __construct( $params = array() ) {

		if ( ! isset( $params['comment_type'], $params['slug'], $params['name'] ) ) {
			trigger_error( 'CommentTrigger requires comment_type, slug and name params.', E_USER_ERROR );
		}

		$this->comment_type = $params['comment_type'];

		parent::__construct( $params['slug'], $params['name'] );

		$this->set_group( __( ucfirst( $this->comment_type ), 'wordpress' ) );

	}

	/**
	 * Registers attached merge tags
	 *
	 * @return void
	 */
	public function merge_tags() {

		$this->add_merge_tag( new MergeTag\Comment\CommentID() );
		$this->add_merge_tag( new MergeTag\Comment\CommentContent() );
		$this->add_merge_tag( new MergeTag\Comment\CommentStatus() );
		$this->add_merge_tag( new MergeTag\Comment\CommentType() );
		$this->add_merge_tag( new MergeTag\Comment\CommentPostID() );
		$this->add_merge_tag( new MergeTag\Comment\CommentPostType() );
		$this->add_merge_tag( new MergeTag\Comment\CommentPostPermalink() );
		$this->add_merge_tag( new MergeTag\Comment\CommentAuthorIP() );
		$this->add_merge_tag( new MergeTag\Comment\CommentAuthorUserAgent() );
		$this->add_merge_tag( new MergeTag\Comment\CommentAuthorUrl() );

		// Author.
		$this->add_merge_tag( new MergeTag\User\UserID( array(
			'slug' => 'comment_author_user_ID',
			'name' => __( 'Comment author user ID', 'notification' ),
		) ) );

        $this->add_merge_tag( new MergeTag\User\UserEmail( array(
			'slug' => 'comment_author_user_email',
			'name' => __( 'Comment author user email', 'notification' ),
		) ) );

		$this->add_merge_tag( new MergeTag\User\UserDisplayName( array(
			'slug' => 'comment_author_user_display_name',
			'name' => __( 'Comment author user display name', 'notification' ),
		) ) );

    }

}
