<?php
/**
 * Story page content
 *
 * @package notification
 */

?>

<div id="notification-story">

	<h1>Wait a moment...</h1>

	<p><?php _e( 'I know you\'ve been using the Notification plugin for a while. That makes me very proud of my work.' ); ?></p>

	<p><?php _e( 'The biggest problem is the plugin popularity. While I truly believe it\'s a very good software, still not to many people know that it exists.' ); ?></p>

	<p><?php _e( 'You can help me change that.' ); ?></p>

	<p><?php _e( 'Think about just one person who would be interested in this plugin.' ); ?></p>

	<p><?php _e( 'Then, send this person a link to this plugin:' ); ?></p>

	<p><code data-clipboard-text="https://wordpress.org/plugins/notification/">https://wordpress.org/plugins/notification/<span><?php esc_html_e( 'copy' ); ?></span></code></p>

	<p><?php _e( 'Thanks for making things happen!' ); ?></p>

	<div class="founder">
		<?php echo get_avatar( 'jakub@underdev.it', 80 ); ?>
		<div class="name">Jakub Mikita</div>
		<div class="title"><?php _e( 'Founder of the Notification plugin' ); ?></div>
	</div>

	<div class="skip">
		<a href="<?php echo admin_url( 'edit.php?post_type=notification&notification-story-skip=true' ); ?>"><?php esc_html_e( 'Skip this screen' ); ?></a>
	</div>

</div>
