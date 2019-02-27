<?php

	if ( ! function_exists( 'wp_notify_moderator' ) &&  ! notification_get_setting( 'integration/emails/notify_moderator' ) ) {
		function wp_notify_moderator() {}
	}

	if ( ! function_exists( 'wp_notify_postauthor' ) && ! notification_get_setting( 'integration/emails/notify_post_author' ) ) {
		function wp_notify_postauthor() {}
	}

	if ( ! function_exists( 'wp_password_change_notification' ) && ! notification_get_setting( 'integration/emails/password_change' ) ) {
		function wp_password_change_notification() {}
	}

	if ( ! function_exists( 'wp_new_user_notification' ) ) {
		function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
			if( notification_get_setting( 'integration/emails/new_user_to_admin' ) ) {
				notification_new_user_notification_to_admin( $user_id, $notify );
			}

			if( notification_get_setting( 'integration/emails/new_user_to_user' ) ) {
				notification_new_user_notification_to_user( $user_id,$deprecated, $notify );
			}
		}

	}

	if ( ( ! notification_get_setting( 'integration/emails/password_forgotten_to_admin' ) || ! notification_get_setting( 'integration/emails/password_forgotten_to_user' ) ) && ! function_exists('dont_send_password_forgotten_email') ) :
		function dont_send_password_forgotten_email( $send = true, $user_id = 0 ) {

			$is_administrator = notification_user_is_administrator( $user_id );

			if ( $is_administrator && ! notification_get_setting( 'integration/emails/password_forgotten_to_admin' ) ) {
				return false;
			}
			if ( ! $is_administrator && ! notification_get_setting( 'integration/emails/password_forgotten_to_user' ) ) {
				return false;
			}

			return $send;

		}

	endif;

	add_filter(' allow_password_reset', 'dont_send_password_forgotten_email');


function notification_new_user_notification_to_admin ( $user_id, $notify = '' ) {

	global $wpdb, $wp_hasher;
	$user = get_userdata( $user_id );

	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	if ( 'user' !== $notify ) {
		$switched_locale = switch_to_locale( get_locale() );
		$message  = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
		$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
		$message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n";

		@wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $message );

		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}
}


function notification_new_user_notification_to_user( $user_id, $deprecated = null, $notify = '' ) {
	if ( $deprecated !== null ) {
		_deprecated_argument( __FUNCTION__, '4.3.1' );
	}

	global $wpdb;
	$user = get_userdata( $user_id );

	if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
		return;
	}

	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$key = wp_generate_password( 20, false );

	do_action( 'retrieve_password_key', $user->user_login, $key );

	if ( empty( $wp_hasher ) ) {
		$wp_hasher = new PasswordHash( 8, true );
	}
	$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

	$switched_locale = switch_to_locale( get_user_locale( $user ) );

	$message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
	$message .= __('To set your password, visit the following address:') . "\r\n\r\n";
	$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

	$message .= wp_login_url() . "\r\n";

	wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);

	if ( $switched_locale ) {
		restore_previous_locale();
	}
}


