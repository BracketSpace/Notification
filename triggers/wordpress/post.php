<?php
/**
 * Post triggers
 */

namespace Notification\Triggers\WordPress\Post;

function published_template() {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';
	$html .= '<p>' . __( '{author_name} just published new post: {post_title}', 'notification' ) . '</p>';
	$html .= '<p>' . __( 'You can read it here: {permalink}', 'notification' ) . '</p>';

	return $html;

}

/**
 * Published
 */
register_trigger( array(
	'slug' => 'wordpress/post/published',
	'name' => __( 'Post published', 'notification' ),
	'group' => __( 'WordPress : Posts', 'notification' ),
	'template' => call_user_func( __NAMESPACE__ . '\\published_template' ),
	'tags' => array(
		'ID'           => 'integer',
		'permalink'    => 'url',
		'post_title'   => 'string',
		'post_name'    => 'string',
		'post_date'    => 'string',
		'post_content' => 'string',
		'post_excerpt' => 'string',
		'author_ID'    => 'integer',
		'author_name'  => 'string',
		'author_email' => 'email'
	)
) );

function published( $new_status, $old_status, $post ) {

	if ( $post->post_type != 'post' ) {
		return;
	}

	if ( $new_status == $old_status ) {
		return;
	}

	notification( 'wordpress/post/published', array(
		'ID'           => $post->ID,
		'permalink'    => get_permalink( $post->ID ),
		'post_title'   => $post->post_title,
		'post_name'    => $post->post_name,
		'post_date'    => $post->post_date,
		'post_content' => $post->post_content,
		'post_excerpt' => $post->post_excerpt,
		'author_ID'    => $post->post_author,
		'author_name'  => get_the_author_meta( 'display_name', $post->post_author ),
		'author_email' => get_the_author_meta( 'user_email', $post->post_author )
	) );

}
add_action( 'transition_post_status', __NAMESPACE__ . '\\published', 10, 3 );

/**
 * Updated
 */
register_trigger( array(
	'slug' => 'wordpress/post/updated',
	'name' => __( 'Post updated', 'notification' ),
	'group' => __( 'WordPress : Posts', 'notification' ),
	'tags' => array(
		'ID'           => 'integer',
		'permalink'    => 'url',
		'post_title'   => 'string',
		'post_name'    => 'string',
		'post_date'    => 'string',
		'post_content' => 'string',
		'post_excerpt' => 'string',
		'author_ID'    => 'integer',
		'author_name'  => 'string',
		'author_email' => 'email'
	)
) );

function updated( $ID, $post ) {

	if ( get_post_type( $post ) != 'post' ) {
		return;
	}

	notification( 'wordpress/post/updated', array(
		'ID'           => $ID,
		'permalink'    => get_permalink( $ID ),
		'post_title'   => $post->post_title,
		'post_name'    => $post->post_name,
		'post_date'    => $post->post_date,
		'post_content' => $post->post_content,
		'post_excerpt' => $post->post_excerpt,
		'author_ID'    => $post->post_author,
		'author_name'  => get_the_author_meta( 'display_name', $post->post_author ),
		'author_email' => get_the_author_meta( 'user_email', $post->post_author )
	) );

}
add_action( 'publish_post', __NAMESPACE__ . '\\updated', 10, 2 );

/**
 * Sent for review
 */
register_trigger( array(
	'slug' => 'wordpress/post/pending_review',
	'name' => __( 'Post sent for review', 'notification' ),
	'group' => __( 'WordPress : Posts', 'notification' ),
	'tags' => array(
		'ID'           => 'integer',
		'permalink'    => 'url',
		'post_title'   => 'string',
		'post_name'    => 'string',
		'post_date'    => 'string',
		'post_content' => 'string',
		'post_excerpt' => 'string',
		'author_ID'    => 'integer',
		'author_name'  => 'string',
		'author_email' => 'email'
	)
) );

function pending_review( $ID, $post ) {

	if ( get_post_type( $post ) != 'post' ) {
		return;
	}

	notification( 'wordpress/post/pending_review', array(
		'ID'           => $ID,
		'permalink'    => get_permalink( $ID ),
		'post_title'   => $post->post_title,
		'post_name'    => $post->post_name,
		'post_date'    => $post->post_date,
		'post_content' => $post->post_content,
		'post_excerpt' => $post->post_excerpt,
		'author_ID'    => $post->post_author,
		'author_name'  => get_the_author_meta( 'display_name', $post->post_author ),
		'author_email' => get_the_author_meta( 'user_email', $post->post_author )
	) );

}
add_action( 'pending_post', __NAMESPACE__ . '\\pending_review', 10, 2 );

/**
 * Trashed
 */
register_trigger( array(
	'slug' => 'wordpress/post/trashed',
	'name' => __( 'Post moved to trash', 'notification' ),
	'group' => __( 'WordPress : Posts', 'notification' ),
	'tags' => array(
		'ID'           => 'integer',
		'post_title'   => 'string',
		'post_date'    => 'string',
		'post_content' => 'string',
		'post_excerpt' => 'string',
		'author_ID'    => 'integer',
		'author_name'  => 'string',
		'author_email' => 'email'
	)
) );

function trashed( $ID, $post ) {

	if ( get_post_type( $post ) != 'post' ) {
		return;
	}

	notification( 'wordpress/post/trashed', array(
		'ID'           => $ID,
		'post_title'   => $post->post_title,
		'post_date'    => $post->post_date,
		'post_content' => $post->post_content,
		'post_excerpt' => $post->post_excerpt,
		'author_ID'    => $post->post_author,
		'author_name'  => get_the_author_meta( 'display_name', $post->post_author ),
		'author_email' => get_the_author_meta( 'user_email', $post->post_author )
	) );

}
add_action( 'trash_post', __NAMESPACE__ . '\\trashed', 10, 2 );
