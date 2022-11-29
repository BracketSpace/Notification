<?php

declare(strict_types=1);

/**
 * Save notification metabox
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $theEsc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>
<div class="submitbox" id="submitpost">
	<?php wp_nonce_field('notification_post_data_save', 'notification_data_nonce'); ?>
	<div class="misc-pub-section">
		<?php do_action('notification/admin/metabox/save/pre', $this); ?>
		<label class="row-label" for="onoffswitch"><strong><?php esc_html_e('Enable', 'notification'); ?></strong></label>
		<div class="onoffswitch">
			<input type="checkbox" name="notification_onoff_switch" class="onoffswitch-checkbox" value="1" id="onoffswitch" <?php checked($get('enabled'), true); ?>>
			<label class="onoffswitch-label" for="onoffswitch">
				<span class="onoffswitch-inner"></span>
				<span class="onoffswitch-switch"></span>
			</label>
		</div>
		<div class="clear"></div>
		<?php do_action('notification/admin/metabox/save/post', $this); ?>
	</div>

	<div id="major-publishing-actions">
		<div id="delete-action">
			<?php $deleteLink = get_delete_post_link($get('post_id'), '', true); ?>
			<?php if (current_user_can('delete_post', $get('post_id')) && $deleteLink) : ?>
				<a class="submitdelete deletion notification-delete-post" href="<?php echo esc_url($deleteLink); ?>"><?php echo esc_html__('Remove', 'notification'); ?></a>
			<?php endif; ?>
		</div>
		<div id="publishing-action">
			<span class="spinner"></span>
			<input type="submit" value="<?php esc_attr_e('Save', 'notification'); ?>" class="button button-primary button-large" id="publish" name="publish">
		</div>
		<div class="clear"></div>
	</div>
</div>
