<?php

	if ( ! function_exists( 'wp_notify_moderator' ) && notification_get_setting( 'integration/emails/comment_await' ) ) {
		function wp_notify_moderator() {}
	}

	if ( ! function_exists( 'wp_notify_postauthor' ) && notification_get_setting( 'integration/emails/comment_published' ) ) {
		function wp_notify_postauthor() {}
	}

	function wp_password_change_notification() { wp_die('test'); }

	if ( ! function_exists( 'wp_new_user_notification' ) && notification_get_setting( 'integration/emails/new_user' ) ) {
		function wp_new_user_notification() {}
	}

	if ( ! function_exists( 'wp_new_blog_notification' ) && notification_get_setting( 'integration/emails/new_blog' ) ) {
		function wp_new_blog_notification() {}
	}
