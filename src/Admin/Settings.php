<?php

/**
 * Settings class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\Settings\CoreFields;
use BracketSpace\Notification\Utils\WpObjectHelper;

/**
 * Settings class
 */
class Settings
{
	/**
	 * Registers General settings
	 *
	 * @param \BracketSpace\Notification\Utils\Settings $settings Settings API object.
	 * @return void
	 */
	public function generalSettings($settings)
	{
		$general = $settings->addSection(__('General', 'notification'), 'general');

		$general->addGroup(__('Content', 'notification'), 'content')
			->addField(
				[
					'name' => __('Empty merge tags', 'notification'),
					'slug' => 'strip_empty_tags',
					'default' => 'true',
					'addons' => [
						'label' => __('Remove unused merge tags from sent values', 'notification'),
					],
					'description' => __('This will affect any notification fields', 'notification'),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Shortcodes', 'notification'),
					'slug' => 'strip_shortcodes',
					'default' => 'true',
					'addons' => [
						'label' => __('Strip all shortcodes', 'notification'),
					],
					'description' => __('This will affect any notification fields', 'notification'),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)->description(__('Notification content settings', 'notification'));

		$general->addGroup(__('Tools', 'notification'), 'tools')
			->addField(
				[
					'name' => __('Wizard', 'notification'),
					'slug' => 'wizard',
					'addons' => [
						'url' => admin_url('edit.php?post_type=notification&page=wizard'),
						'label' => __('Run wizard', 'notification'),
					],
					'render' => [new CoreFields\Button(), 'input'],
				]
			)->description(__('Plugin tools', 'notification'));

		$general->addGroup(__('Advanced', 'notification'), 'advanced')
			->addField(
				[
					'name' => __('Background processing', 'notification'),
					'slug' => 'background_processing',
					'addons' => [
						'label' => __('Enable background processing with WP Cron', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
					'description' => __(
						'By enabling this setting, no Trigger will be executed immediately.
						Instead the execution will be saved into
						WP Cron system and executed in a few minutes.
						This can be helpful when the execution is spread over a few requests, ie.
						using Gutenberg editor.',
						'notification'
					),
				]
			);

		$general->addGroup(__('Uninstallation', 'notification'), 'uninstallation')
			->addField(
				[
					'name' => __('Notifications', 'notification'),
					'slug' => 'notifications',
					'default' => 'true',
					'addons' => [
						'label' => __('Remove all added notifications', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Settings', 'notification'),
					'slug' => 'settings',
					'default' => 'true',
					'addons' => [
						'label' => __('Remove plugin settings', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Licenses', 'notification'),
					'slug' => 'licenses',
					'default' => 'true',
					'addons' => [
						'label' => __('Remove and deactivate extension licenses', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)->description(__('Choose what to remove upon plugin removal', 'notification'));
	}

	/**
	 * Registers Triggers settings
	 *
	 * @param \BracketSpace\Notification\Utils\Settings $settings Settings API object.
	 * @return void
	 */
	public function triggersSettings($settings)
	{
		$triggers = $settings->addSection(__('Triggers', 'notification'), 'triggers');

		$triggers->addGroup(__('Post', 'notification'), 'post_types')
			->addField(
				[
					'name' => __('Post Types', 'notification'),
					'slug' => 'types',
					'default' => ['post', 'page'],
					'addons' => [
						'multiple' => true,
						'pretty' => true,
						'options' => static function () {
							return apply_filters(
								'notification/settings/triggers/valid_post_types',
								WpObjectHelper::getPostTypes(['public' => true])
							);
						},
					],
					'render' => [new CoreFields\Select(), 'input'],
					'sanitize' => [new CoreFields\Select(), 'sanitize'],
				]
			)->description(
				__(
					'For these post types you will be able to define published,
					updated, pending moderation etc. notifications',
					'notification'
				)
			);

		$triggers->addGroup(__('Taxonomy', 'notification'), 'taxonomies')
			->addField(
				[
					'name' => __('Taxonomies', 'notification'),
					'slug' => 'types',
					'default' => ['category', 'post_tag'],
					'addons' => [
						'multiple' => true,
						'pretty' => true,
						'options' => static function () {
							return apply_filters(
								'notification/settings/triggers/valid_taxonomies',
								WpObjectHelper::getTaxonomies(['public' => true])
							);
						},
					],
					'render' => [new CoreFields\Select(), 'input'],
					'sanitize' => [new CoreFields\Select(), 'sanitize'],
				]
			)
			->description(
				__(
					'For these taxonomies you will be able to define published, updated and deleted notifications',
					'notification'
				)
			);

		$triggers->addGroup(__('Comment', 'notification'), 'comment')
			->addField(
				[
					'name' => __('Comment Types', 'notification'),
					'slug' => 'types',
					'default' => ['comment'],
					'addons' => [
						'multiple' => true,
						'pretty' => true,
						'options' => static function () {
							return WpObjectHelper::getCommentTypes();
						},
					],
					'render' => [new CoreFields\Select(), 'input'],
					'sanitize' => [new CoreFields\Select(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Akismet', 'notification'),
					'slug' => 'akismet',
					'default' => 'true',
					'addons' => [
						'label' => __(
							'Do not send notification if comment has been marked as a spam by Akismet',
							'notification'
						),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			);

		$triggers->addGroup(__('User', 'notification'), 'user')
			->addField(
				[
					'name' => __('User', 'notification'),
					'slug' => 'enable',
					'default' => 'true',
					'addons' => [
						'label' => __('Enable user triggers', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			);

		$triggers->addGroup(__('Media', 'notification'), 'media')
			->addField(
				[
					'name' => __('Media', 'notification'),
					'slug' => 'enable',
					'default' => 'true',
					'addons' => [
						'label' => __('Enable media triggers', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			);

		$triggers->addGroup(__('Theme', 'notification'), 'theme')
			->addField(
				[
					'name' => __('Theme', 'notification'),
					'slug' => 'enable',
					'default' => 'true',
					'addons' => [
						'label' => __('Enable theme triggers', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			);

		$triggers->addGroup(__('Plugin', 'notification'), 'plugin')
			->addField(
				[
					'name' => __('Plugin', 'notification'),
					'slug' => 'enable',
					'default' => 'true',
					'addons' => [
						'label' => __('Enable plugin triggers', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			);

		// phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled
		$triggers->addGroup(__('WordPress', 'notification'), 'wordpress')
			->addField(
				[
					'name' => __('Updates', 'notification'),
					'slug' => 'updates',
					'default' => false,
					'addons' => [
						'label' => __('Enable "Updates available" trigger', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Send if no updates', 'notification'),
					'slug' => 'updates_send_anyway',
					'default' => false,
					'addons' => [
						'label' => __('Send updates email even if no updates available', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Updates check period', 'notification'),
					'slug' => 'updates_cron_period',
					'default' => 'ntfn_week',
					'addons' => [
						'options' => static function () {
							$options = [];
							foreach (wp_get_schedules() as $scheduleName => $schedule) {
								$options[$scheduleName] = $schedule['display'];
							}
							return $options;
						},
					],
					'render' => [new CoreFields\Select(), 'input'],
					'sanitize' => [new CoreFields\Select(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Site email address change request', 'notification'),
					'slug' => 'email_address_change_request',
					'default' => 'true',
					'addons' => [
						'label' => __('Enable site email address change request trigger', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			);

		$triggers->addGroup(__('Privacy', 'notification'), 'privacy')
			->addField(
				[
					'name' => __('Privacy', 'notification'),
					'slug' => 'enable',
					'default' => 'true',
					'addons' => [
						'label' => __('Enable privacy triggers', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			);
	}

	/**
	 * Registers Carrier settings
	 *
	 * @param \BracketSpace\Notification\Utils\Settings $settings Settings API object.
	 * @return void
	 */
	public function carriersSettings($settings)
	{
		if (!empty($_SERVER['SERVER_NAME'])) {
			$sitename = strtolower(sanitize_text_field(wp_unslash($_SERVER['SERVER_NAME'])));
			if (substr($sitename, 0, 4) === 'www.') {
				$sitename = substr($sitename, 4);
			}
		} else {
			$sitename = 'example.com';
		}

		$defaultFromEmail = 'wordpress@' . $sitename;

		$carriers = $settings->addSection(__('Carriers', 'notification'), 'carriers');

		$carriers->addGroup(__('Email', 'notification'), 'email')
			->addField(
				[
					'name' => __('Enable', 'notification'),
					'slug' => 'enable',
					'default' => 'true',
					'addons' => [
						'label' => __('Enable Email Carrier', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Message type', 'notification'),
					'slug' => 'type',
					'default' => 'html',
					'addons' => [
						'options' => [
							'html' => __('HTML', 'notification'),
							'plain' => __('Plain text', 'notification'),
						],
					],
					'render' => [new CoreFields\Select(), 'input'],
					'sanitize' => [new CoreFields\Select(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Unfiltered HTML', 'notification'),
					'slug' => 'unfiltered_html',
					'default' => false,
					'addons' => [
						'label' => __('Allow unfiltered HTML in email body', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
					'description' => __(
						'This will change the Visual editor to code editor with HTML syntax',
						'notification'
					),
				]
			)
			->addField(
				[
					'name' => __('From Name', 'notification'),
					'slug' => 'from_name',
					'default' => '',
					'render' => [new CoreFields\Text(), 'input'],
					'sanitize' => [new CoreFields\Text(), 'sanitize'],
					'description' => sprintf(
						// Translators: %s default value.
						__('Leave blank to use default value: %s', 'notification'),
						'<code>WordPress</code>'
					),
				]
			)
			->addField(
				[
					'name' => __('From Email', 'notification'),
					'slug' => 'from_email',
					'default' => '',
					'render' => [new CoreFields\Text(), 'input'],
					'sanitize' => [new CoreFields\Text(), 'sanitize'],
					'description' => sprintf(
						// Translators: %s default value.
						__('Leave blank to use default value: %s', 'notification'),
						'<code>' . $defaultFromEmail . '</code>'
					),
				]
			)
			->addField(
				[
					'name' => __('Headers', 'notification'),
					'slug' => 'headers',
					'default' => false,
					'addons' => [
						'label' => __('Allow to configure email headers', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			);

		$carriers->addGroup(__('Webhook', 'notification'), 'webhook')
			->addField(
				[
					'name' => __('Enable', 'notification'),
					'slug' => 'enable',
					'default' => 'true',
					'addons' => [
						'label' => __('Enable Webhook Carrier', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Headers', 'notification'),
					'slug' => 'headers',
					'default' => false,
					'addons' => [
						'label' => __('Allow to configure webhook headers', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			);
	}

	/**
	 * Registers Emails settings
	 *
	 * @param \BracketSpace\Notification\Utils\Settings $settings Settings API object.
	 * @return void
	 */
	public function emailsSettings($settings)
	{
		$general = $settings->addSection(__('Integration', 'notification'), 'integration');

		$general->addGroup(__('Default WordPress emails', 'notification'), 'emails')
			->addField(
				[
					'name' => __('New user', 'notification'),
					'slug' => 'new_user_to_admin',
					'default' => false,
					'addons' => [
						'label' => __('Disable new user email to admin', 'notification'),
					],
					'description' => __('Email is sent after registration.', 'notification'),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Welcome email', 'notification'),
					'slug' => 'new_user_to_user',
					'default' => false,
					'addons' => [
						'label' => __('Disable account details email to <strong>user</strong>', 'notification'),
					],
					'description' => __(
						'Email is sent after registration and contains password setup link.',
						'notification'
					),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('New comment', 'notification'),
					'slug' => 'post_author',
					'default' => false,
					'addons' => [
						'label' => __(
							'Disable email to <strong>post author</strong> about a new comment',
							'notification'
						),
					],
					'description' => __('Email is sent after comment is published.', 'notification'),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Comment awaiting moderation', 'notification'),
					'slug' => 'comment_moderator',
					'default' => false,
					'addons' => [
						'label' => __(
							'Disable email to <strong>moderator (admin)</strong> when new comment awaits moderation',
							'notification'
						),
					],
					'description' => __('Email is sent when new comment is awaiting approval.', 'notification'),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Password reset request', 'notification'),
					'slug' => 'password_forgotten_to_user',
					'default' => false,
					'addons' => [
						'label' => __(
							'Disable email to <strong>user</strong> with password reset link',
							'notification'
						),
					],
					'description' => __(
						'Email is sent when user fills out the password reset request.',
						'notification'
					),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Password changed', 'notification'),
					'slug' => 'password_change_to_admin',
					'default' => false,
					'addons' => [
						'label' => __(
							'Disable email to <strong>admin</strong> when user changed their password',
							'notification'
						),
					],
					'description' => __('Email is sent when user changes his password.', 'notification'),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Password changed', 'notification'),
					'slug' => 'password_change_to_user',
					'default' => false,
					'addons' => [
						'label' => __(
							'Disable email to <strong>user</strong> when their password has been changed',
							'notification'
						),
					],
					'description' => __('Email is sent when user changes his password.', 'notification'),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Email address changed', 'notification'),
					'slug' => 'email_change_to_user',
					'default' => false,
					'addons' => [
						'label' => __(
							'Disable email to <strong>user</strong> about profile email address change',
							'notification'
						),
					],
					'description' => __(
						'Email is sent when user saves a new email address in his profile.',
						'notification'
					),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Email address change request', 'notification'),
					'slug' => 'send_confirmation_on_profile_email',
					'default' => false,
					'addons' => [
						'label' => __(
							'Disable email to <strong>user</strong> about email address change request',
							'notification'
						),
					],
					'description' => __(
						'Email is sent when user requests email address change.',
						'notification'
					),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Admin Email address change request', 'notification'),
					'slug' => 'send_confirmation_on_admin_email',
					'default' => false,
					'addons' => [
						'label' => __(
							'Disable email to <strong>new admin</strong> about site email address change request.',
							'notification'
						),
					],
					'description' => __(
						'Email is sent when site email address change is requested.',
						'notification'
					),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Admin Email address changed', 'notification'),
					'slug' => 'send_confirmation_on_admin_email_changed',
					'default' => false,
					'addons' => [
						'label' => __(
							'Disable email to <strong>new admin</strong> about site email address changed.',
							'notification'
						),
					],
					'description' => __('Email is sent when new site email address is confirmed.', 'notification'),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Automatic WordPress core update', 'notification'),
					'slug' => 'automatic_wp_core_update',
					'default' => false,
					'addons' => [
						'label' => __(
							'Disable email to <strong>admin</strong> about successful background update',
							'notification'
						),
					],
					'description' => __(
						'Email is sent when background updates finishes successfully.
						"Failed update" email will always be sent to admin.',
						'notification'
					),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)->description(__('Disable each default emails by selecting the option.', 'notification'));
	}

	/**
	 * Filters post types from supported posts
	 *
	 * @filter notification/settings/triggers/valid_post_types
	 *
	 * @param array<mixed> $postTypes post types.
	 * @return array<mixed>
	 * @since  5.0.0
	 */
	public function filterPostTypes($postTypes)
	{
		if (isset($postTypes['attachment'])) {
			unset($postTypes['attachment']);
		}

		return $postTypes;
	}
}
