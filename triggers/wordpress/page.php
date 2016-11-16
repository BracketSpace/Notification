<?php
/**
 * Page triggers
 */

namespace Notification\Triggers\WordPress\Page;

/**
 * Published
 */

function published_template() {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';
	$html .= '<p>' . __( '{author_name} just published new page: {post_title}', 'notification' ) . '</p>';
	$html .= '<p>' . __( 'You can read it here: {permalink}', 'notification' ) . '</p>';

	return $html;

}

register_trigger( array(
	'slug'     => 'wordpress/page/published',
	'name'     => __( 'Page published', 'notification' ),
	'group'    => __( 'WordPress : Pages', 'notification' ),
	'template' => call_user_func( __NAMESPACE__ . '\\published_template' ),
	'tags'     => array(
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

function published( $new_status, $old_status, $post ) {

	if ( $post->post_type != 'page' ) {
		return;
	}

	if ( $new_status == $old_status ) {
		return;
	}

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
add_action( 'publish_page', __NAMESPACE__ . '\\published', 10, 3 );

/**
 * Updated
 */

function updated_template() {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';
	$html .= '<p>' . __( '{author_name} just updated page: {post_title}', 'notification' ) . '</p>';
	$html .= '<p>' . __( 'You can read it here: {permalink}', 'notification' ) . '</p>';

	return $html;

}

register_trigger( array(
	'slug'     => 'wordpress/page/updated',
	'name'     => __( 'Page updated', 'notification' ),
	'group'    => __( 'WordPress : Pages', 'notification' ),
	'template' => call_user_func( __NAMESPACE__ . '\\updated_template' ),
	'tags'     => array(
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

function updated( $ID, $post ) {

	notification( 'wordpress/page/updated', array(
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
add_action( 'publish_page', __NAMESPACE__ . '\\updated', 10, 2 );

/**
 * Sent for review
 */

function pending_review_template() {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';
	$html .= '<p>' . __( '{author_name} just send his brand new page for review: {post_title}', 'notification' ) . '</p>';
	$html .= '<p><a href="http://site.address/wp-admin/post.php?post={ID}&action=edit">' . __( 'Review now', 'notification' ) . '</a></p>';

	return $html;

}

register_trigger( array(
	'slug'     => 'wordpress/page/pending_review',
	'name'     => __( 'Page sent for review', 'notification' ),
	'group'    => __( 'WordPress : Pages', 'notification' ),
	'template' => call_user_func( __NAMESPACE__ . '\\pending_review_template' ),
	'tags'     => array(
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

function trashed_template() {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';
	$html .= '<p>' . __( '{author_name} just moved page: {post_title} to the trash.', 'notification' ) . '</p>';
	$html .= '<p><a href="http://site.address/wp-admin/edit.php?post_status=trash&post_type=page">' . __( 'View trash', 'notification' ) . '</a></p>';

	return $html;

}

register_trigger( array(
	'slug'     => 'wordpress/page/trashed',
	'name'     => __( 'Page moved to trash', 'notification' ),
	'group'    => __( 'WordPress : Pages', 'notification' ),
	'template' => call_user_func( __NAMESPACE__ . '\\trashed_template' ),
	'tags'     => array(
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
