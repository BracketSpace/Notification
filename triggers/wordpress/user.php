<?php
/**
 * User triggers
 */

namespace underDEV\Notification\Triggers\WordPress\User;

use underDEV\Notification\Settings;

/**
 * Templates
 */

function registered_template() {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . __( 'New user {user_login} <{user_email}> just registered on your site.', 'notification' ). '</p>';

	return $html;

}

function profile_updated_template() {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . __( 'User {user_login} <{user_email}> just updated his profile.', 'notification' ). '</p>';

	return $html;

}

function logged_in_template() {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . __( 'User {user_login} <{user_email}> just logged in.', 'notification' ). '</p>';

	return $html;

}

function deleted_template() {

	$html = '<p>' . __( 'Howdy!', 'notification' ) . '</p>';

	/* translators: please do not translate strings in { } */
	$html .= '<p>' . __( 'User {user_login} <{user_email}> account has been deleted.', 'notification' ). '</p>';

	return $html;

}


/**
 * Triggers
 */

$settings = Settings::get()->get_settings();

if ( isset( $settings['general']['enabled_triggers']['user'] ) && ! empty( $settings['general']['enabled_triggers']['user'] ) ) :

	// Registered

	if ( apply_filters( 'notification/triggers/default/wordpress/user/registered', true ) ) :

		register_trigger( array(
			'slug'     => 'wordpress/user/registered',
			'name'     => __( 'User registered', 'notification' ),
			'group'    => __( 'WordPress : User', 'notification' ),
			'template' => call_user_func( __NAMESPACE__ . '\\registered_template' ),
			'tags'     => array(
				'user_ID'                  => 'integer',
				'user_login'               => 'string',
				'user_email'               => 'email',
				'user_registered_datetime' => 'string',
				'user_role'                => 'string'
			)
		) );

		if ( is_notification_defined( 'wordpress/user/registered' ) ) {

			add_action( 'user_register', function( $user_ID ) {

				$userdata = get_userdata( $user_ID );
				$user     = $userdata->data;
				$role     = \translate_user_role( ucfirst( $userdata->roles[0] ) );

				notification( 'wordpress/user/registered', array(
					'user_ID'                  => $user->ID,
					'user_login'               => $user->user_login,
					'user_email'               => $user->user_email,
					'user_registered_datetime' => $user->user_registered,
					'user_role'                => $role
				) );

			}, 10, 1 );

		}

	endif;

	// Profile updated

	if ( apply_filters( 'notification/triggers/default/wordpress/user/profile_updated', true ) ) :

		register_trigger( array(
			'slug'     => 'wordpress/user/profile_updated',
			'name'     => __( 'User profile updated', 'notification' ),
			'group'    => __( 'WordPress : User', 'notification' ),
			'template' => call_user_func( __NAMESPACE__ . '\\profile_updated_template' ),
			'disable'  => array( 'user' ),
			'tags'     => array(
				'user_ID'                  => 'integer',
				'user_login'               => 'string',
				'user_email'               => 'email',
				'user_registered_datetime' => 'string',
				'user_role'                => 'string',
				'user_first_name'          => 'string',
				'user_last_name'           => 'string',
				'user_bio'                 => 'string'
			)
		) );

		if ( is_notification_defined( 'wordpress/user/profile_updated' ) ) {

			add_action( 'profile_update', function( $user_ID ) {

				$userdata = get_userdata( $user_ID );
				$role     = \translate_user_role( ucfirst( $userdata->roles[0] ) );

				notification( 'wordpress/user/profile_updated', array(
					'user_ID'                  => $userdata->ID,
					'user_login'               => $userdata->user_login,
					'user_email'               => $userdata->user_email,
					'user_registered_datetime' => $userdata->user_registered,
					'user_role'                => $role,
					'user_first_name'          => $userdata->first_name,
					'user_last_name'           => $userdata->last_name,
					'user_bio'                 => nl2br( $userdata->description )
				), array(
					'user' => $userdata->ID
				) );

			}, 10, 1 );

		}

	endif;

	// Logged in

	if ( apply_filters( 'notification/triggers/default/wordpress/user/logged_in', true ) ) :

		register_trigger( array(
			'slug'     => 'wordpress/user/logged_in',
			'name'     => __( 'User logged in', 'notification' ),
			'group'    => __( 'WordPress : User', 'notification' ),
			'template' => call_user_func( __NAMESPACE__ . '\\logged_in_template' ),
			'disable'  => array( 'user' ),
			'tags'     => array(
				'user_ID'                  => 'integer',
				'user_login'               => 'string',
				'user_email'               => 'email',
				'user_registered_datetime' => 'string',
				'user_logged_in_datetime'  => 'string',
				'user_role'                => 'string',
				'user_first_name'          => 'string',
				'user_last_name'           => 'string',
				'user_bio'                 => 'string'
			)
		) );

		if ( is_notification_defined( 'wordpress/user/logged_in' ) ) {

			add_action( 'wp_login', function( $user_login, $userdata ) {

				$role     = \translate_user_role( ucfirst( $userdata->roles[0] ) );

				notification( 'wordpress/user/logged_in', array(
					'user_ID'                  => $userdata->ID,
					'user_login'               => $userdata->user_login,
					'user_email'               => $userdata->user_email,
					'user_registered_datetime' => $userdata->user_registered,
					'user_logged_in_datetime'  => date( 'Y-m-d H:i:s' ),
					'user_role'                => $role,
					'user_first_name'          => $userdata->first_name,
					'user_last_name'           => $userdata->last_name,
					'user_bio'                 => nl2br( $userdata->description )
				), array(
					'user' => $userdata->ID
				) );

			}, 10, 2 );

		}

	endif;

	// Deleted

	if ( apply_filters( 'notification/triggers/default/wordpress/user/deleted', true ) ) :

		register_trigger( array(
			'slug'     => 'wordpress/user/deleted',
			'name'     => __( 'User deleted', 'notification' ),
			'group'    => __( 'WordPress : User', 'notification' ),
			'template' => call_user_func( __NAMESPACE__ . '\\deleted_template' ),
			'disable'  => array( 'user' ),
			'tags'     => array(
				'user_ID'                  => 'integer',
				'user_login'               => 'string',
				'user_email'               => 'email',
				'user_registered_datetime' => 'string',
				'user_deleted_datetime'    => 'string',
				'user_role'                => 'string',
				'user_first_name'          => 'string',
				'user_last_name'           => 'string',
				'user_bio'                 => 'string'
			)
		) );

		if ( is_notification_defined( 'wordpress/user/deleted' ) ) {

			add_action( 'delete_user', function( $user_ID ) {

				$userdata = get_userdata( $user_ID );
				$role     = \translate_user_role( ucfirst( $userdata->roles[0] ) );

				notification( 'wordpress/user/deleted', array(
					'user_ID'                  => $userdata->ID,
					'user_login'               => $userdata->user_login,
					'user_email'               => $userdata->user_email,
					'user_registered_datetime' => $userdata->user_registered,
					'user_deleted_datetime'    => date( 'Y-m-d H:i:s' ),
					'user_role'                => $role,
					'user_first_name'          => $userdata->first_name,
					'user_last_name'           => $userdata->last_name,
					'user_bio'                 => nl2br( $userdata->description )
				), array(
					'user' => $userdata->ID
				) );

			}, 10, 1 );

		}

	endif;

endif;
