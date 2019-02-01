<?php
/**
 * Story page content
 *
 * @package notification
 */

?>

<div id="notification-story">

	<h1>Wait a moment...</h1>

	<p><?php esc_html_e( 'I know you\'ve been using the Notification plugin for a while. That makes me very proud of my work.', 'notification' ); ?></p>

	<p><?php esc_html_e( 'The biggest problem is the plugin popularity. While I truly believe it\'s a very good software, still not to many people know that it exists.', 'notification' ); ?></p>

	<p><?php esc_html_e( 'You can help me change that.', 'notification' ); ?></p>

	<p><?php esc_html_e( 'Think about just one person who would be interested in this plugin.', 'notification' ); ?></p>

	<p><?php esc_html_e( 'Then, send this person a link to this plugin:', 'notification' ); ?></p>

	<p><code data-clipboard-text="https://wordpress.org/plugins/notification/">https://wordpress.org/plugins/notification/<span><?php esc_html_e( 'copy', 'notification' ); ?></span></code></p>

	<p><?php esc_html_e( 'Thanks for making things happen!', 'notification' ); ?></p>

	<div class="founder">
		<?php echo get_avatar( 'jakub@underdev.it', 80 ); ?>
		<div class="name">Jakub Mikita</div>
		<div class="title"><?php esc_html_e( 'Founder of the Notification plugin', 'notification' ); ?></div>
	</div>

	<div class="skip">
		<a href="<?php echo admin_url( 'edit.php?post_type=notification&notification-story-skip=true' ); // phpcs:ignore ?>"><?php esc_html_e( 'Skip this screen', 'notification' ); ?></a>
	</div>

</div>
