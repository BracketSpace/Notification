<?php
/**
 * Wizard content
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var callable(string $var_name, string $default=): void $the_esc Escaped variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>

<div id="notifications-wizard">

	<form action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" method="post">
		<?php wp_nonce_field( 'notification_wizard' ); ?>
		<input type="hidden" name="action" value="save_notification_wizard">

		<div class="content">
			<h1><?php esc_html_e( 'Notification Wizard', 'notification' ); ?></h1>
			<h3><?php esc_html_e( 'Quickly setup the Notifications you need.', 'notification' ); ?><br/>
			<?php esc_html_e( 'You\'ll be able to edit them later.', 'notification' ); ?></h3>

			<?php foreach ( (array) $get( 'sections' ) as $section ) : ?>
				<div class="notifications-group">
					<h2 class="notifications-group-title">
						<?php esc_html_e( $section['name'] ); ?>
					</h2>
					<div class="notifications-list">
						<?php foreach ( $section['items'] as $item ) : ?>
							<div class="notifications-tile">
								<div class="inside">
									<div class="content">
										<h2 class="hndle">
											<label>
												<input type="checkbox" name="notification_wizard[]" value="<?php esc_html_e( $item['slug'] ); ?>">
												<?php esc_html_e( $item['name'] ); ?>
											</label>
										</h2>
										<p><?php esc_html_e( $item['description'] ); ?></p>
										<?php foreach ( $item['recipients'] as $recipient ) : ?>
											<div class="trigger-type">
												<span class="dashicons dashicons-admin-users"></span>
												<?php esc_html_e( $recipient['name'] ); ?>
											</div>
										<?php endforeach; ?>
									</div>
									<div class="carrier-type"><?php esc_html_e( 'Email', 'notification' ); ?></div>
								</div>
								<div class="notifications-tile-hover">
									<span class="dashicons dashicons-plus"></span><?php esc_html_e( 'Add this notification', 'notification' ); ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<aside class="sidebar">
			<div class="sidebar-content">
				<h3>Useful links</h3>
				<a href="https://docs.bracketspace.com/notification/user-guide/who-can-use-this-plugin" target="_blank">
					<?php esc_html_e( 'Who can use this plugin', 'notification' ); ?>
				</a>
				<a href="https://docs.bracketspace.com/notification/user-guide/how-notification-plugin-works" target="_blank">
					<?php esc_html_e( 'How Notification plugin works', 'notification' ); ?>
				</a>
				<a href="https://docs.bracketspace.com/notification/developer/general/extension-possibilities" target="_blank">
					<?php esc_html_e( 'Extension possibilities', 'notification' ); ?>
				</a>
				<button type="submit" name="submit" class="button button-primary button-large create-notifications hidden"></button>
				<button type="submit" name="skip-wizard" class="button button-secondary skip-wizard">
					<?php esc_html_e( 'Skip the Wizard', 'notification' ); ?>
				</button>
			</div>
		</aside>
	</form>
</div>
