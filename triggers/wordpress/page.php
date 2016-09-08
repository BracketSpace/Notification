<?php
/**
 * Page triggers
 */

namespace Notification\Triggers\WordPress\Page;

/**
 * Published
 */
register_trigger( array(
	'slug' => 'wordpress/page/published',
	'name' => __( 'Page published', 'notification' ),
	'group' => __( 'WordPress : Pages', 'notification' ),
	'tags' => array(
		'ID'           => 'integer',
		'permalink'    => 'url',
		'page_title'   => 'string',
		'page_name'    => 'string',
		'page_date'    => 'string',
		'page_content' => 'string',
		'author_ID'    => 'integer',
		'author_name'  => 'string',
		'author_email' => 'email'
	)
) );

function published( $ID, $post ) {

	notification( 'wordpress/page/published', array(
		'ID'           => $ID,
		'permalink'    => get_permalink( $ID ),
		'page_title'   => $post->post_title,
		'page_name'    => $post->post_name,
		'page_date'    => $post->post_date,
		'page_content' => $post->post_content,
		'author_ID'    => $post->post_author,
		'author_name'  => get_the_author_meta( 'display_name', $post->post_author ),
		'author_email' => get_the_author_meta( 'user_email', $post->post_author )
	) );

}
add_action( 'publish_page', __NAMESPACE__ . '\\published', 10, 2 );

/**
 * Sent for review
 */
register_trigger( array(
	'slug' => 'wordpress/page/pending_review',
	'name' => __( 'Page sent for review', 'notification' ),
	'group' => __( 'WordPress : Pages', 'notification' ),
	'tags' => array(
		'ID'           => 'integer',
		'permalink'    => 'url',
		'page_title'   => 'string',
		'page_name'    => 'string',
		'page_date'    => 'string',
		'page_content' => 'string',
		'author_ID'    => 'integer',
		'author_name'  => 'string',
		'author_email' => 'email'
	)
) );

function pending_review( $new_status, $old_status, $post ) {

	notification( 'wordpress/page/pending_review', array(
		'ID'           => $ID,
		'permalink'    => get_permalink( $ID ),
		'page_title'   => $post->post_title,
		'page_name'    => $post->post_name,
		'page_date'    => $post->post_date,
		'page_content' => $post->post_content,
		'author_ID'    => $post->post_author,
		'author_name'  => get_the_author_meta( 'display_name', $post->post_author ),
		'author_email' => get_the_author_meta( 'user_email', $post->post_author )
	) );

}
add_action( 'pending_page', __NAMESPACE__ . '\\pending_review', 10, 3 );

/**
 * Trashed
 */
register_trigger( array(
	'slug' => 'wordpress/page/trashed',
	'name' => __( 'Page moved to trash', 'notification' ),
	'group' => __( 'WordPress : Pages', 'notification' ),
	'tags' => array(
		'ID'           => 'integer',
		'page_title'   => 'string',
		'page_date'    => 'string',
		'page_content' => 'string',
		'author_ID'    => 'integer',
		'author_name'  => 'string',
		'author_email' => 'email'
	)
) );

function trashed( $new_status, $old_status, $post ) {

	notification( 'wordpress/post/trashed', array(
		'ID'           => $ID,
		'page_title'   => $post->post_title,
		'page_date'    => $post->post_date,
		'page_content' => $post->post_content,
		'author_ID'    => $post->post_author,
		'author_name'  => get_the_author_meta( 'display_name', $post->post_author ),
		'author_email' => get_the_author_meta( 'user_email', $post->post_author )
	) );

}
add_action( 'trash_page', __NAMESPACE__ . '\\trashed', 10, 3 );
