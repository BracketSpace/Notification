<?php
/**
 * Post Types triggers
 */

namespace Notification\Triggers\WordPress\PostTypes;

use Notification\Settings;

/**
 * Templates
 */

function published_template( $post_type = 'post' ) {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . sprintf( __( '{author_name} just published new %1$s: {%1$s_title}', 'notification' ), $post_type ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . __( 'You can read it here: {permalink}', 'notification' ) . '</p>';

	return $html;

}

function updated_template( $post_type = 'post' ) {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . sprintf( __( '{author_name} just updated %1$s: {%1$s_title}', 'notification' ), $post_type ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . __( 'You can read it here: {permalink}', 'notification' ) . '</p>';

	return $html;

}

function pending_review_template( $post_type = 'post' ) {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . sprintf( __( '{author_name} just send his brand new %1$s for review: {%1$s_title}', 'notification' ), $post_type ) . '</p>';

	$html .= '<p><a href="' . admin_url( 'post.php?post={ID}&action=edit' ) . '">' . __( 'Review now', 'notification' ) . '</a></p>';

	return $html;

}

function trashed_template( $post_type = 'post' ) {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . sprintf( __( '{author_name} just moved %1$s: {%1$s_title} to the trash.', 'notification' ), $post_type ) . '</p>';

	$html .= '<p><a href="' . admin_url( 'edit.php?post_status=trash&post_type=' . $post_type ) . '">' . __( 'View trash', 'notification' ) . '</a></p>';

	return $html;

}

/**
 * Notification funcitons
 */

function published( $new_status, $old_status, $post ) {

	if ( $post->post_type != $post_type ) {
		return;
	}

	if ( $new_status == $old_status ) {
		return;
	}

	if ( $new_status != 'publish' ) {
		return;
	}

	notification( 'wordpress/' . $post_type . '/published', array(
		'ID'                    => $post->ID,
		'permalink'             => get_permalink( $post->ID ),
		$post_type . '_title'   => $post->post_title,
		$post_type . '_name'    => $post->post_name,
		$post_type . '_date'    => $post->post_date,
		$post_type . '_content' => $post->post_content,
		$post_type . '_excerpt' => $post->post_excerpt,
		'author_ID'             => $post->post_author,
		'author_name'           => get_the_author_meta( 'display_name', $post->post_author ),
		'author_email'          => get_the_author_meta( 'user_email', $post->post_author )
	) );

}

function updated( $ID, $post ) {

	if ( get_post_type( $post ) != $post_type ) {
		return;
	}

	notification( 'wordpress/' . $post_type . '/updated', array(
		'ID'                    => $post->ID,
		'permalink'             => get_permalink( $post->ID ),
		$post_type . '_title'   => $post->post_title,
		$post_type . '_name'    => $post->post_name,
		$post_type . '_date'    => $post->post_date,
		$post_type . '_content' => $post->post_content,
		$post_type . '_excerpt' => $post->post_excerpt,
		'author_ID'             => $post->post_author,
		'author_name'           => get_the_author_meta( 'display_name', $post->post_author ),
		'author_email'          => get_the_author_meta( 'user_email', $post->post_author )
	) );

}

function pending_review( $ID, $post ) {

	if ( get_post_type( $post ) != $post_type ) {
		return;
	}

	notification( 'wordpress/' . $post_type . '/pending_review', array(
		'ID'                    => $post->ID,
		'permalink'             => get_permalink( $post->ID ),
		$post_type . '_title'   => $post->post_title,
		$post_type . '_name'    => $post->post_name,
		$post_type . '_date'    => $post->post_date,
		$post_type . '_content' => $post->post_content,
		$post_type . '_excerpt' => $post->post_excerpt,
		'author_ID'             => $post->post_author,
		'author_name'           => get_the_author_meta( 'display_name', $post->post_author ),
		'author_email'          => get_the_author_meta( 'user_email', $post->post_author )
	) );

}

function trashed( $ID, $post ) {

	if ( get_post_type( $post ) != $post_type ) {
		return;
	}

	notification( 'wordpress/' . $post_type . '/trashed', array(
		'ID'                    => $post->ID,
		'permalink'             => get_permalink( $post->ID ),
		$post_type . '_title'   => $post->post_title,
		$post_type . '_name'    => $post->post_name,
		$post_type . '_date'    => $post->post_date,
		$post_type . '_content' => $post->post_content,
		$post_type . '_excerpt' => $post->post_excerpt,
		'author_ID'             => $post->post_author,
		'author_name'           => get_the_author_meta( 'display_name', $post->post_author ),
		'author_email'          => get_the_author_meta( 'user_email', $post->post_author )
	) );

}

/**
 * Triggers
 */

$settings = Settings::get()->get_settings();

foreach ( $settings['general']['post_types_triggers']['post_types'] as $post_type ) :

	if ( ! apply_filters( 'notification/triggers/default/wordpress/post_types/' . $post_type, true ) ) {
		continue;
	}

	// Published

	if ( apply_filters( 'notification/triggers/default/wordpress/post_types/' . $post_type . '/published', true ) ) :

		register_trigger( array(
			'slug'     => 'wordpress/' . $post_type . '/published',
			'name'     => sprintf( __( '%s published', 'notification' ), ucfirst( $post_type ) ),
			'group'    => sprintf( __( 'WordPress : %s', 'notification' ), ucfirst( $post_type ) ),
			'template' => call_user_func( __NAMESPACE__ . '\\published_template', $post_type ),
			'tags'     => array(
				'ID'                    => 'integer',
				'permalink'             => 'url',
				$post_type . '_title'   => 'string',
				$post_type . '_name'    => 'string',
				$post_type . '_date'    => 'string',
				$post_type . '_content' => 'string',
				$post_type . '_excerpt' => 'string',
				'author_ID'             => 'integer',
				'author_name'           => 'string',
				'author_email'          => 'email'
			)
		) );

		if ( is_notification_defined( 'wordpress/' . $post_type . '/published' ) ) {
			add_action( 'transition_post_status', __NAMESPACE__ . '\\published', 10, 3 );
		}

	endif;

	// Updated

	if ( apply_filters( 'notification/triggers/default/wordpress/post_types/' . $post_type . '/updated', true ) ) :

		register_trigger( array(
			'slug'     => 'wordpress/' . $post_type . '/updated',
			'name'     => sprintf( __( '%s updated', 'notification' ), ucfirst( $post_type ) ),
			'group'    => sprintf( __( 'WordPress : %s', 'notification' ), ucfirst( $post_type ) ),
			'template' => call_user_func( __NAMESPACE__ . '\\updated_template', $post_type ),
			'tags'     => array(
				'ID'                    => 'integer',
				'permalink'             => 'url',
				$post_type . '_title'   => 'string',
				$post_type . '_name'    => 'string',
				$post_type . '_date'    => 'string',
				$post_type . '_content' => 'string',
				$post_type . '_excerpt' => 'string',
				'author_ID'             => 'integer',
				'author_name'           => 'string',
				'author_email'          => 'email'
			)
		) );

		if ( is_notification_defined( 'wordpress/' . $post_type . '/updated' ) ) {
			add_action( 'publish_' . $post_type , __NAMESPACE__ . '\\updated', 10, 2 );
		}

	endif;

	// Sent for review

	if ( apply_filters( 'notification/triggers/default/wordpress/post_types/' . $post_type . '/pending_review', true ) ) :

		register_trigger( array(
			'slug'     => 'wordpress/' . $post_type . '/pending_review',
			'name'     => sprintf( __( '%s sent for review', 'notification' ), ucfirst( $post_type ) ),
			'group'    => sprintf( __( 'WordPress : %s', 'notification' ), ucfirst( $post_type ) ),
			'template' => call_user_func( __NAMESPACE__ . '\\pending_review_template', $post_type ),
			'tags'     => array(
				'ID'                    => 'integer',
				'permalink'             => 'url',
				$post_type . '_title'   => 'string',
				$post_type . '_name'    => 'string',
				$post_type . '_date'    => 'string',
				$post_type . '_content' => 'string',
				$post_type . '_excerpt' => 'string',
				'author_ID'             => 'integer',
				'author_name'           => 'string',
				'author_email'          => 'email'
			)
		) );

		if ( is_notification_defined( 'wordpress/' . $post_type . '/pending_review' ) ) {
			add_action( 'pending_' . $post_type , __NAMESPACE__ . '\\pending_review', 10, 2 );
		}

	endif;

	// Trashed

	if ( apply_filters( 'notification/triggers/default/wordpress/post_types/' . $post_type . '/trashed', true ) ) :

		register_trigger( array(
			'slug'     => 'wordpress/' . $post_type . '/trashed',
			'name'     => sprintf( __( '%s moved to trash', 'notification' ), ucfirst( $post_type ) ),
			'group'    => sprintf( __( 'WordPress : %s', 'notification' ), ucfirst( $post_type ) ),
			'template' => call_user_func( __NAMESPACE__ . '\\pending_review_template', $post_type ),
			'tags'     => array(
				'ID'                    => 'integer',
				'permalink'             => 'url',
				$post_type . '_title'   => 'string',
				$post_type . '_name'    => 'string',
				$post_type . '_date'    => 'string',
				$post_type . '_content' => 'string',
				$post_type . '_excerpt' => 'string',
				'author_ID'             => 'integer',
				'author_name'           => 'string',
				'author_email'          => 'email'
			)
		) );

		if ( is_notification_defined( 'wordpress/' . $post_type . '/trashed' ) ) {
			add_action( 'trash_' . $post_type , __NAMESPACE__ . '\\trashed', 10, 2 );
		}

	endif;

endforeach;

