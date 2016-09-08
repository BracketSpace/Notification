<?php
/**
 * Pingback triggers
 */

namespace Notification\Triggers\WordPress\Pingback;

/**
 * Published
 */
register_trigger( array(
	'slug' => 'wordpress/pingback/added',
	'name' => __( 'Pingback added', 'notification' ),
	'group' => __( 'WordPress : Pingbacks', 'notification' ),
	'tags' => array(
		'ID'               => 'integer',
		'post_ID'          => 'integer',
		'author_name'      => 'string',
		'author_email'     => 'email',
		'author_url'       => 'url',
		'author_IP'        => 'string',
		'author_user_id'   => 'integer',
		'author_agent'     => 'string',
		'comment_date'     => 'string',
		'comment_content'  => 'string',
		'comment_approved' => 'string',
		'comment_type'     => 'string',
	)
) );

function added( $ID, $comment ) {

	if ( $comment->comment_type != 'pingback' ) {
		return;
	}

	notification( 'wordpress/pingback/added', array(
		'ID'               => $ID,
		'post_ID'          => $comment->comment_post_ID,
		'author_name'      => $comment->comment_author,
		'author_email'     => $comment->comment_author_email,
		'author_url'       => $comment->comment_author_url,
		'author_IP'        => $comment->comment_author_IP,
		'author_user_id'   => $comment->user_id,
		'author_agent'     => $comment->comment_agent,
		'comment_date'     => $comment->comment_date,
		'comment_content'  => $comment->comment_content,
		'comment_approved' => $comment->comment_approved,
		'comment_type'     => $comment->comment_type,
	) );

}
add_action( 'wp_insert_comment', __NAMESPACE__ . '\\added', 10, 2 );

/**
 * Approved
 */
register_trigger( array(
	'slug' => 'wordpress/pingback/approved',
	'name' => __( 'Pingback approved', 'notification' ),
	'group' => __( 'WordPress : Pingbacks', 'notification' ),
	'tags' => array(
		'ID'               => 'integer',
		'post_ID'          => 'integer',
		'author_name'      => 'string',
		'author_email'     => 'email',
		'author_url'       => 'url',
		'author_IP'        => 'string',
		'author_user_id'   => 'integer',
		'author_agent'     => 'string',
		'comment_date'     => 'string',
		'comment_content'  => 'string',
		'comment_approved' => 'string',
		'comment_type'     => 'string',
	)
) );

function approved( $ID, $comment ) {

	notification( 'wordpress/pingback/approved', array(
		'ID'               => $ID,
		'post_ID'          => $comment->comment_post_ID,
		'author_name'      => $comment->comment_author,
		'author_email'     => $comment->comment_author_email,
		'author_url'       => $comment->comment_author_url,
		'author_IP'        => $comment->comment_author_IP,
		'author_user_id'   => $comment->user_id,
		'author_agent'     => $comment->comment_agent,
		'comment_date'     => $comment->comment_date,
		'comment_content'  => $comment->comment_content,
		'comment_approved' => $comment->comment_approved,
		'comment_type'     => $comment->comment_type,
	) );

}
add_action( 'comment_approved_pingback', __NAMESPACE__ . '\\approved', 10, 2 );

/**
 * Unapproved
 */
register_trigger( array(
	'slug' => 'wordpress/pingback/unapproved',
	'name' => __( 'Pingback unapproved', 'notification' ),
	'group' => __( 'WordPress : Pingbacks', 'notification' ),
	'tags' => array(
		'ID'               => 'integer',
		'post_ID'          => 'integer',
		'author_name'      => 'string',
		'author_email'     => 'email',
		'author_url'       => 'url',
		'author_IP'        => 'string',
		'author_user_id'   => 'integer',
		'author_agent'     => 'string',
		'comment_date'     => 'string',
		'comment_content'  => 'string',
		'comment_approved' => 'string',
		'comment_type'     => 'string',
	)
) );

function unapproved( $ID, $comment ) {

	notification( 'wordpress/pingback/unapproved', array(
		'ID'               => $ID,
		'post_ID'          => $comment->comment_post_ID,
		'author_name'      => $comment->comment_author,
		'author_email'     => $comment->comment_author_email,
		'author_url'       => $comment->comment_author_url,
		'author_IP'        => $comment->comment_author_IP,
		'author_user_id'   => $comment->user_id,
		'author_agent'     => $comment->comment_agent,
		'comment_date'     => $comment->comment_date,
		'comment_content'  => $comment->comment_content,
		'comment_approved' => $comment->comment_approved,
		'comment_type'     => $comment->comment_type,
	) );

}
add_action( 'comment_unapproved_pingback', __NAMESPACE__ . '\\unapproved', 10, 2 );

/**
 * Trashed
 */
register_trigger( array(
	'slug' => 'wordpress/pingback/trashed',
	'name' => __( 'Pingback moved to trash', 'notification' ),
	'group' => __( 'WordPress : Pingbacks', 'notification' ),
	'tags' => array(
		'ID'               => 'integer',
		'post_ID'          => 'integer',
		'author_name'      => 'string',
		'author_email'     => 'email',
		'author_url'       => 'url',
		'author_IP'        => 'string',
		'author_user_id'   => 'integer',
		'author_agent'     => 'string',
		'comment_date'     => 'string',
		'comment_content'  => 'string',
		'comment_approved' => 'string',
		'comment_type'     => 'string',
	)
) );

function trashed( $ID, $comment ) {

	notification( 'wordpress/pingback/trashed', array(
		'ID'               => $ID,
		'post_ID'          => $comment->comment_post_ID,
		'author_name'      => $comment->comment_author,
		'author_email'     => $comment->comment_author_email,
		'author_url'       => $comment->comment_author_url,
		'author_IP'        => $comment->comment_author_IP,
		'author_user_id'   => $comment->user_id,
		'author_agent'     => $comment->comment_agent,
		'comment_date'     => $comment->comment_date,
		'comment_content'  => $comment->comment_content,
		'comment_approved' => $comment->comment_approved,
		'comment_type'     => $comment->comment_type,
	) );

}
add_action( 'comment_trash_pingback', __NAMESPACE__ . '\\trashed', 10, 2 );

/**
 * Marked as spam
 */
register_trigger( array(
	'slug' => 'wordpress/pingback/spam',
	'name' => __( 'Pingback marked as spam', 'notification' ),
	'group' => __( 'WordPress : Pingbacks', 'notification' ),
	'tags' => array(
		'ID'               => 'integer',
		'post_ID'          => 'integer',
		'author_name'      => 'string',
		'author_email'     => 'email',
		'author_url'       => 'url',
		'author_IP'        => 'string',
		'author_user_id'   => 'integer',
		'author_agent'     => 'string',
		'comment_date'     => 'string',
		'comment_content'  => 'string',
		'comment_approved' => 'string',
		'comment_type'     => 'string',
	)
) );

function spam( $ID, $comment ) {

	notification( 'wordpress/pingback/spam', array(
		'ID'               => $ID,
		'post_ID'          => $comment->comment_post_ID,
		'author_name'      => $comment->comment_author,
		'author_email'     => $comment->comment_author_email,
		'author_url'       => $comment->comment_author_url,
		'author_IP'        => $comment->comment_author_IP,
		'author_user_id'   => $comment->user_id,
		'author_agent'     => $comment->comment_agent,
		'comment_date'     => $comment->comment_date,
		'comment_content'  => $comment->comment_content,
		'comment_approved' => $comment->comment_approved,
		'comment_type'     => $comment->comment_type,
	) );

}
add_action( 'comment_trash_pingback', __NAMESPACE__ . '\\spam', 10, 2 );
