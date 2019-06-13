<?php
/**
 * Wizard content
 *
 * @package notification
 */

?>

<div id="notifications-wizard" data-selected-notifications-count="0">

	<div class="content">
		<h1><?php esc_html_e( 'Notification Wizard', 'notification' ); ?></h1>
		<h3><?php esc_html_e( 'Quickly setup the Notifications you need.', 'notification' ); ?><br/>
		<?php esc_html_e( 'You\'ll be able to edit them later.', 'notification' ); ?></h3>

		<div class="notifications-group">
			<h2 class="notifications-group-title">
				<?php esc_html_e( 'Common Notifications', 'notification' ); ?>
			</h2>
			<!-- <h4 class="notifications-group-subtitle">
				Select the email to overwrite it
			</h4> -->
			<div class="notifications-list">
				<?php for ( $i = 0; $i <= 10; $i++ ) : ?>
				<div class="notifications-tile">
					<div class="inside">
						<div class="content">
							<h2 class="hndle"><label><input type="checkbox" name="notification_name"> Post published and even more text here</label></h2>
							<p>When post is accepted and published an email is sent to the post author.</p>
							<div class="trigger-type"><span class="dashicons dashicons-admin-users"></span>Post author</div>
						</div>
						<div class="carrier-type">Email</div>
					</div>
					<div class="notifications-tile-hover">
						<span class="dashicons dashicons-plus"></span>
						Add this notification
					</div>
				</div>
				<?php endfor ?>
			</div>

		</div>

	</div>
	<aside class="sidebar">
		<div class="sidebar-content">
			<h3>Useful links</h3>
			<a href="#">Who can use this plugin</a>
			<a href="#">How Notification plugin works</a>
			<a href="#">Extension possibilities</a>
			<a href="#" class="button button-primary button-large create-notifications hidden"></a>
			<a href="#" class="button button-secondary skip-wizard">Skip the Wizard</a>
		</div>
	</aside>
</div>
