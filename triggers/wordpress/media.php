<?php
/**
 * Media triggers
 */

namespace underDEV\Notification\Triggers\WordPress\Media;

use underDEV\Notification\Settings;

/**
 * Templates
 */

function added_template() {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . __( '{author_name} just added new attachment: {attachment_title}.', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . __( 'You can check it here: {attachment_direct_url}', 'notification' ) . '</p>';

	return $html;

}

function updated_template() {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . __( '{author_name} just updated an attachment: {attachment_title}.', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . __( 'You can check it here: {attachment_direct_url}', 'notification' ) . '</p>';

	return $html;

}

function trashed_template() {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . __( '{author_name} just removed the {attachment_title} attachment.', 'notification' ) . '</p>';

	return $html;

}

/**
 * Triggers
 */

$settings = Settings::get()->get_settings();

if ( isset( $settings['general']['enabled_triggers']['media'] ) && ! empty( $settings['general']['enabled_triggers']['media'] ) ) :

	$trigger_tags = array(
		'ID'                    => 'integer',
		'attachment_page'       => 'url',
		'attachment_title'      => 'string',
		'attachment_date'       => 'string',
		'attachment_mime_type'  => 'string',
		'attachment_direct_url' => 'url',
		'author_ID'             => 'integer',
		'author_name'           => 'string',
		'author_email'          => 'email',
		'author_login'          => 'string'
	);

	// Added

	if ( apply_filters( 'notification/triggers/default/wordpress/media/added', true ) ) :

		register_trigger( array(
			'slug'     => 'wordpress/media/added',
			'name'     => __( 'Attachment uploaded', 'notification' ),
			'group'    => __( 'WordPress : Attachment', 'notification' ),
			'template' => call_user_func( __NAMESPACE__ . '\\added_template' ),
			'disable'  => array( 'user' ),
			'tags'     => $trigger_tags
		) );

		if ( is_notification_defined( 'wordpress/media/added' ) ) {

			add_action( 'add_attachment', function( $post_id ) {

				$post = get_post( $post_id );

				$tag_values = array(
					'ID'                    => $post->ID,
					'attachment_page'       => get_permalink( $post->ID ),
					'attachment_title'      => $post->post_title,
					'attachment_date'       => $post->post_date,
					'attachment_mime_type'  => $post->post_mime_type,
					'attachment_direct_url' => $post->guid,
					'author_ID'             => $post->post_author,
					'author_name'           => get_the_author_meta( 'display_name', $post->post_author ),
					'author_email'          => get_the_author_meta( 'user_email', $post->post_author ),
					'author_login'          => get_the_author_meta( 'user_login', $post->post_author )
				);

				notification( 'wordpress/media/added', $tag_values, array(
					'user' => array(
						$post->post_author,
						get_current_user_id()
					)
				) );

			}, 10, 1 );

		}

	endif;

	// Updated

	if ( apply_filters( 'notification/triggers/default/wordpress/media/updated', true ) ) :

		$update_trigger_tags = array_merge( $trigger_tags, array(
			'updating_user_ID'    => 'integer',
			'updating_user_name'  => 'string',
			'updating_user_email' => 'email',
			'updating_user_login' => 'string'
		) );

		register_trigger( array(
			'slug'     => 'wordpress/media/updated',
			'name'     => __( 'Attachment updated', 'notification' ),
			'group'    => __( 'WordPress : Attachment', 'notification' ),
			'template' => call_user_func( __NAMESPACE__ . '\\updated_template' ),
			'disable'  => array( 'user' ),
			'tags'     => $update_trigger_tags
		) );

		if ( is_notification_defined( 'wordpress/media/updated' ) ) {

			add_action( 'attachment_updated', function( $ID, $post, $post_before ) {

				if ( get_post_type( $post ) != 'attachment' || empty( $post->post_name ) ) {
					return;
				}

				$updating_user = get_current_user_id();

				$tag_values = array(
					'ID'                    => $post->ID,
					'attachment_page'       => get_permalink( $post->ID ),
					'attachment_title'      => $post->post_title,
					'attachment_date'       => $post->post_date,
					'attachment_mime_type'  => $post->post_mime_type,
					'attachment_direct_url' => $post->guid,
					'author_ID'             => $post->post_author,
					'author_name'           => get_the_author_meta( 'display_name', $post->post_author ),
					'author_email'          => get_the_author_meta( 'user_email', $post->post_author ),
					'author_login'          => get_the_author_meta( 'user_login', $post->post_author ),
					'updating_user_ID'      => $updating_user,
					'updating_user_name'    => get_the_author_meta( 'display_name', $updating_user ),
					'updating_user_email'   => get_the_author_meta( 'user_email', $updating_user ),
					'updating_user_login'   => get_the_author_meta( 'user_login', $updating_user )
				);

				notification( 'wordpress/media/updated', $tag_values, array(
					'user' => array(
						$post->post_author,
						get_current_user_id()
					)
				) );

			}, 10, 3 );

		}

	endif;

	// Trashed

	if ( apply_filters( 'notification/triggers/default/wordpress/media/trashed', true ) ) :

		$trash_trigger_tags = array_merge( $trigger_tags, array(
			'trashing_user_ID'    => 'integer',
			'trashing_user_name'  => 'string',
			'trashing_user_email' => 'email',
			'trashing_user_login' => 'string'
		) );

		register_trigger( array(
			'slug'     => 'wordpress/media/trashed',
			'name'     => __( 'Attachment deleted', 'notification' ),
			'group'    => __( 'WordPress : Attachment', 'notification' ),
			'template' => call_user_func( __NAMESPACE__ . '\\trashed_template' ),
			'disable'  => array( 'user' ),
			'tags'     => $trash_trigger_tags
		) );

		if ( is_notification_defined( 'wordpress/media/trashed' ) ) {

			add_action( 'delete_attachment', function( $ID ) {

				$post = get_post( $ID );

				if ( get_post_type( $post ) != 'attachment' ) {
					return;
				}

				$trashing_user = get_current_user_id();

				$tag_values = array(
					'ID'                    => $post->ID,
					'attachment_page'       => get_permalink( $post->ID ),
					'attachment_title'      => $post->post_title,
					'attachment_date'       => $post->post_date,
					'attachment_mime_type'  => $post->post_mime_type,
					'attachment_direct_url' => $post->guid,
					'author_ID'             => $post->post_author,
					'author_name'           => get_the_author_meta( 'display_name', $post->post_author ),
					'author_email'          => get_the_author_meta( 'user_email', $post->post_author ),
					'author_login'          => get_the_author_meta( 'user_login', $post->post_author ),
					'trashing_user_ID'      => $trashing_user,
					'trashing_user_name'    => get_the_author_meta( 'display_name', $trashing_user ),
					'trashing_user_email'   => get_the_author_meta( 'user_email', $trashing_user ),
					'trashing_user_login'   => get_the_author_meta( 'user_login', $trashing_user )
				);

				notification( 'wordpress/media/trashed', $tag_values, array(
					'user' => array(
						$post->post_author,
						get_current_user_id()
					)
				) );

			}, 10, 1 );

		}

	endif;

endif;
