<?php
/**
 * Post Types triggers
 */

namespace underDEV\Notification\Triggers\WordPress\PostTypes;

use underDEV\Notification\Settings;

/**
 * Custom functions
 */

function post_terms( $post_id, $taxonomy ) {

	return implode( ', ', wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'names' ) ) );

}

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
 * Triggers
 */

$settings = Settings::get()->get_settings();

if ( isset( $settings['general']['enabled_triggers']['post_types'] ) && ! empty( $settings['general']['enabled_triggers']['post_types'] ) ) :

	foreach ( $settings['general']['enabled_triggers']['post_types'] as $post_type ) :

		/**
		 * @deprecated 2.0 Do not use this filter
		 */
		if ( ! apply_filters( 'notification/triggers/default/wordpress/' . $post_type, true ) ) {
			continue;
		}

		if ( ! apply_filters( 'notification/triggers/default/wordpress/post_types/' . $post_type, true ) ) {
			continue;
		}

		$post_type_name = get_post_type_object( $post_type )->labels->singular_name;

		$post_taxonomies = get_object_taxonomies( $post_type );

		// Published

		if ( apply_filters( 'notification/triggers/default/wordpress/post_types/' . $post_type . '/published', true ) ) :

			$tags = array(
				'ID'                    => 'integer',
				'permalink'             => 'url',
				$post_type . '_title'   => 'string',
				$post_type . '_name'    => 'string',
				$post_type . '_date'    => 'string',
				$post_type . '_content' => 'string',
				$post_type . '_excerpt' => 'string',
				'author_ID'             => 'integer',
				'author_name'           => 'string',
				'author_email'          => 'email',
				'author_login'          => 'string'
			);

			foreach ( $post_taxonomies as $taxonomy_name ) {
				$tags[ $taxonomy_name . '_terms' ] = 'string';
			}

			register_trigger( array(
				'slug'     => 'wordpress/' . $post_type . '/published',
				'name'     => sprintf( __( '%s published', 'notification' ), $post_type_name ),
				'group'    => ucfirst( $post_type ),
				'template' => call_user_func( __NAMESPACE__ . '\\published_template', $post_type ),
				'disable'  => array( 'post', 'user' ),
				'tags'     => $tags
			) );

			if ( is_notification_defined( 'wordpress/' . $post_type . '/published' ) ) {

				add_action( 'transition_post_status', function( $new_status, $old_status, $post ) use ( $post_type, $post_taxonomies ) {

					if ( $post->post_type != $post_type ) {
						return;
					}

					if ( $new_status == $old_status ) {
						return;
					}

					if ( $new_status != 'publish' ) {
						return;
					}

					$tag_values = array(
						'ID'                    => $post->ID,
						'permalink'             => get_permalink( $post->ID ),
						$post_type . '_title'   => $post->post_title,
						$post_type . '_name'    => $post->post_name,
						$post_type . '_date'    => $post->post_date,
						$post_type . '_content' => $post->post_content,
						$post_type . '_excerpt' => $post->post_excerpt,
						'author_ID'             => $post->post_author,
						'author_name'           => get_the_author_meta( 'display_name', $post->post_author ),
						'author_email'          => get_the_author_meta( 'user_email', $post->post_author ),
						'author_login'          => get_the_author_meta( 'user_login', $post->post_author )
					);

					foreach ( $post_taxonomies as $taxonomy_name ) {
						$tag_values[ $taxonomy_name . '_terms' ] = call_user_func( __NAMESPACE__ . '\\post_terms', $post->ID, $taxonomy_name );
					}

					notification( 'wordpress/' . $post_type . '/published', $tag_values, array(
						'post' => $post->ID,
						'user' => array(
							$post->post_author,
							get_current_user_id()
						)
					) );

				}, 10, 3 );

			}

		endif;

		// Updated

		if ( apply_filters( 'notification/triggers/default/wordpress/post_types/' . $post_type . '/updated', true ) ) :

			$tags = array(
				'ID'                    => 'integer',
				'permalink'             => 'url',
				$post_type . '_title'   => 'string',
				$post_type . '_name'    => 'string',
				$post_type . '_date'    => 'string',
				$post_type . '_content' => 'string',
				$post_type . '_excerpt' => 'string',
				'author_ID'             => 'integer',
				'author_name'           => 'string',
				'author_email'          => 'email',
				'author_login'          => 'string'
			);

			foreach ( $post_taxonomies as $taxonomy_name ) {
				$tags[ $taxonomy_name . '_terms' ] = 'string';
			}

			register_trigger( array(
				'slug'     => 'wordpress/' . $post_type . '/updated',
				'name'     => sprintf( __( '%s updated', 'notification' ), $post_type_name ),
				'group'    => ucfirst( $post_type ),
				'template' => call_user_func( __NAMESPACE__ . '\\updated_template', $post_type ),
				'disable'  => array( 'post', 'user' ),
				'tags'     => $tags
			) );

			if ( is_notification_defined( 'wordpress/' . $post_type . '/updated' ) ) {

				add_action( 'post_updated', function( $ID, $post, $post_before ) use ( $post_type, $post_taxonomies ) {

					if ( get_post_type( $post ) != $post_type || empty( $post->post_name ) || $post_before->post_status != 'publish' ) {
						return;
					}

					$tag_values = array(
						'ID'                    => $post->ID,
						'permalink'             => get_permalink( $post->ID ),
						$post_type . '_title'   => $post->post_title,
						$post_type . '_name'    => $post->post_name,
						$post_type . '_date'    => $post->post_date,
						$post_type . '_content' => $post->post_content,
						$post_type . '_excerpt' => $post->post_excerpt,
						'author_ID'             => $post->post_author,
						'author_name'           => get_the_author_meta( 'display_name', $post->post_author ),
						'author_email'          => get_the_author_meta( 'user_email', $post->post_author ),
						'author_login'          => get_the_author_meta( 'user_login', $post->post_author )
					);

					foreach ( $post_taxonomies as $taxonomy_name ) {
						$tag_values[ $taxonomy_name . '_terms' ] = call_user_func( __NAMESPACE__ . '\\post_terms', $post->ID, $taxonomy_name );
					}

					notification( 'wordpress/' . $post_type . '/updated', $tag_values, array(
						'post' => $post->ID,
						'user' => array(
							$post->post_author,
							get_current_user_id()
						)
					) );

				}, 10, 3 );

			}

		endif;

		// Sent for review

		if ( apply_filters( 'notification/triggers/default/wordpress/post_types/' . $post_type . '/pending_review', true ) ) :

			$tags = array(
				'ID'                    => 'integer',
				'permalink'             => 'url',
				$post_type . '_title'   => 'string',
				$post_type . '_name'    => 'string',
				$post_type . '_date'    => 'string',
				$post_type . '_content' => 'string',
				$post_type . '_excerpt' => 'string',
				'author_ID'             => 'integer',
				'author_name'           => 'string',
				'author_email'          => 'email',
				'author_login'          => 'string'
			);

			foreach ( $post_taxonomies as $taxonomy_name ) {
				$tags[ $taxonomy_name . '_terms' ] = 'string';
			}

			register_trigger( array(
				'slug'     => 'wordpress/' . $post_type . '/pending_review',
				'name'     => sprintf( __( '%s sent for review', 'notification' ), $post_type_name ),
				'group'    => ucfirst( $post_type ),
				'template' => call_user_func( __NAMESPACE__ . '\\pending_review_template', $post_type ),
				'disable'  => array( 'post', 'user' ),
				'tags'     => $tags
			) );

			if ( is_notification_defined( 'wordpress/' . $post_type . '/pending_review' ) ) {

				add_action( 'pending_' . $post_type, function( $ID, $post ) use ( $post_type, $post_taxonomies ) {

					if ( get_post_type( $post ) != $post_type ) {
						return;
					}

					$tag_values = array(
						'ID'                    => $post->ID,
						'permalink'             => get_permalink( $post->ID ),
						$post_type . '_title'   => $post->post_title,
						$post_type . '_name'    => $post->post_name,
						$post_type . '_date'    => $post->post_date,
						$post_type . '_content' => $post->post_content,
						$post_type . '_excerpt' => $post->post_excerpt,
						'author_ID'             => $post->post_author,
						'author_name'           => get_the_author_meta( 'display_name', $post->post_author ),
						'author_email'          => get_the_author_meta( 'user_email', $post->post_author ),
						'author_login'          => get_the_author_meta( 'user_login', $post->post_author )
					);

					foreach ( $post_taxonomies as $taxonomy_name ) {
						$tag_values[ $taxonomy_name . '_terms' ] = call_user_func( __NAMESPACE__ . '\\post_terms', $post->ID, $taxonomy_name );
					}

					notification( 'wordpress/' . $post_type . '/pending_review', $tag_values, array(
						'post' => $post->ID,
						'user' => array(
							$post->post_author,
							get_current_user_id()
						)
					) );

				}, 10, 2 );

			}

		endif;

		// Trashed

		if ( apply_filters( 'notification/triggers/default/wordpress/post_types/' . $post_type . '/trashed', true ) ) :

			$tags = array(
				'ID'                    => 'integer',
				'permalink'             => 'url',
				$post_type . '_title'   => 'string',
				$post_type . '_name'    => 'string',
				$post_type . '_date'    => 'string',
				$post_type . '_content' => 'string',
				$post_type . '_excerpt' => 'string',
				'author_ID'             => 'integer',
				'author_name'           => 'string',
				'author_email'          => 'email',
				'author_login'          => 'string'
			);

			foreach ( $post_taxonomies as $taxonomy_name ) {
				$tags[ $taxonomy_name . '_terms' ] = 'string';
			}

			register_trigger( array(
				'slug'     => 'wordpress/' . $post_type . '/trashed',
				'name'     => sprintf( __( '%s moved to trash', 'notification' ), $post_type_name ),
				'group'    => ucfirst( $post_type ),
				'template' => call_user_func( __NAMESPACE__ . '\\trashed_template', $post_type ),
				'disable'  => array( 'post', 'user' ),
				'tags'     => $tags
			) );

			if ( is_notification_defined( 'wordpress/' . $post_type . '/trashed' ) ) {

				add_action( 'trash_' . $post_type, function( $ID, $post ) use ( $post_type, $post_taxonomies ) {

					if ( get_post_type( $post ) != $post_type ) {
						return;
					}

					$tag_values = array(
						'ID'                    => $post->ID,
						'permalink'             => get_permalink( $post->ID ),
						$post_type . '_title'   => $post->post_title,
						$post_type . '_name'    => $post->post_name,
						$post_type . '_date'    => $post->post_date,
						$post_type . '_content' => $post->post_content,
						$post_type . '_excerpt' => $post->post_excerpt,
						'author_ID'             => $post->post_author,
						'author_name'           => get_the_author_meta( 'display_name', $post->post_author ),
						'author_email'          => get_the_author_meta( 'user_email', $post->post_author ),
						'author_login'          => get_the_author_meta( 'user_login', $post->post_author )
					);

					foreach ( $post_taxonomies as $taxonomy_name ) {
						$tag_values[ $taxonomy_name . '_terms' ] = call_user_func( __NAMESPACE__ . '\\post_terms', $post->ID, $taxonomy_name );
					}

					notification( 'wordpress/' . $post_type . '/trashed', $tag_values, array(
						'post' => $post->ID,
						'user' => array(
							$post->post_author,
							get_current_user_id()
						)
					) );

				}, 10, 2 );

			}

		endif;

	endforeach;

endif;
