<?php
/**
 * WordPress integration class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Integration;

/**
 * WordPress integration class
 */
class WordPress {

	/**
	 * Filters default Email From Name
	 *
	 * @filter wp_mail_from_name 1000
	 *
	 * @since  5.2.2
	 * @param  string $from_name Default From Name.
	 * @return string
	 */
	public function filter_email_from_name( $from_name ) {

		$setting = notification_get_setting( 'notifications/email/from_name' );

		return empty( $setting ) ? $from_name : $setting;

	}

	/**
	 * Filters default Email From Email
	 *
	 * @filter wp_mail_from 1000
	 *
	 * @since  5.2.2
	 * @param  string $from_email Default From Email.
	 * @return string
	 */
	public function filter_email_from_email( $from_email ) {

		$setting = notification_get_setting( 'notifications/email/from_email' );

		return empty( $setting ) ? $from_email : $setting;

	}

	/**
	 * --------------------------
	 * Comment replied proxies
	 * --------------------------
	 */

	/**
	 * Proxies the wp_insert_comment action to check
	 * if comment is a reply.
	 *
	 * @action wp_insert_comment
	 *
	 * @since 5.3.1
	 * @param integer $comment_id Comment ID.
	 * @param object  $comment    Comment object.
	 * @return void
	 */
	public function proxy_comment_reply( $comment_id, $comment ) {
		$status = '1' === $comment->comment_approved ? 'approved' : 'unapproved';
		do_action( 'notification_insert_comment_proxy', $status, 'insert', $comment );
	}

	/**
	 * --------------------------
	 * Comment published proxies
	 * --------------------------
	 */

	/**
	 * Proxies the comment_post action
	 *
	 * @action comment_post
	 *
	 * @since 6.2.0
	 * @param integer $comment_id Comment ID.
	 * @param object  $approved   If Comment is approved.
	 * @return void
	 */
	public function proxy_post_comment_to_published( $comment_id, $approved ) {
		if ( $approved ) {
			do_action( 'notification_comment_published_proxy', get_comment( $comment_id ) );
		}
	}

	/**
	 * Proxies the transition_comment_status action
	 *
	 * @action transition_comment_status
	 *
	 * @since 6.2.0
	 * @param string $comment_new_status New comment status.
	 * @param string $comment_old_status Old comment status.
	 * @param object $comment            Comment object.
	 * @return void
	 */
	public function proxy_transition_comment_status_to_published( $comment_new_status, $comment_old_status, $comment ) {

		if ( 'spam' === $comment->comment_approved && notification_get_setting( 'triggers/comment/akismet' ) ) {
			return;
		}

		if ( $comment_new_status === $comment_old_status || 'approved' !== $comment_new_status ) {
			return;
		}

		do_action( 'notification_comment_published_proxy', $comment );

	}

}
