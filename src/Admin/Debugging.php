<?php

/**
 * Admin debugging class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Utils\Settings\CoreFields;
use BracketSpace\Notification\Utils\Settings\Fields as SpecificFields;

/**
 * Debugging class
 */
class Debugging
{
	/**
	 * Registers Debugging settings
	 *
	 * @action notification/settings/register 70
	 *
	 * @param \BracketSpace\Notification\Utils\Settings $settings Settings API object.
	 * @return void
	 */
	public function debuggingSettings($settings)
	{
		$debugging = $settings->addSection(__('Debugging', 'notification'), 'debugging');

		$debugging->addGroup(__('Settings', 'notification'), 'settings')
			->addField(
				[
					'name' => __('Notification log', 'notification'),
					'slug' => 'debug_log',
					'default' => false,
					'addons' => [
						'label' => __('Enable Notification logging', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Suppress Notifications', 'notification'),
					'slug' => 'debug_suppressing',
					'default' => 'true',
					'addons' => [
						'label' => __('Suppress Notifications while logging is active', 'notification'),
					],
					'description' => __(
						'While suppressing is active, no notifications are sent',
						'notification'
					),
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Error log', 'notification'),
					'slug' => 'error_log',
					'default' => false,
					'addons' => [
						'label' => __('Enable error logging', 'notification'),
					],
					'render' => [new CoreFields\Checkbox(), 'input'],
					'sanitize' => [new CoreFields\Checkbox(), 'sanitize'],
				]
			)
			->addField(
				[
					'name' => __('Clear', 'notification'),
					'slug' => 'clear',
					'default' => false,
					'addons' => [
						'message' => '
						<a href="' . admin_url(
							'admin-post.php?action=notification_clear_logs&log_type=notification&nonce=' .
								wp_create_nonce('notification_clear_log_notification')
						) . '" class="button button-secondary">' . esc_html__('Clear Notification logs') .
							'</a>
							<a href="' . admin_url(
								'admin-post.php?action=notification_clear_logs&log_type=error&nonce=' .
									wp_create_nonce('notification_clear_log_error')
							) . '" class="button button-secondary">' . esc_html__('Clear Error logs') .
							'</a>
					',
					],
					'render' => [new CoreFields\Message(), 'input'],
					'sanitize' => [new CoreFields\Message(), 'sanitize'],
				]
			);

		$debugging->addGroup(__('Notification Log', 'notification'), 'notification_log')
			->addField(
				[
					'name' => __('Log', 'notification'),
					'slug' => 'log',
					'render' => [new SpecificFields\NotificationLog(), 'input'],
					'sanitize' => '__return_null',
				]
			);

		$debugging->addGroup(__('Error Log', 'notification'), 'error_log')
			->addField(
				[
					'name' => __('Log', 'notification'),
					'slug' => 'log',
					'render' => [new SpecificFields\ErrorLog(), 'input'],
					'sanitize' => '__return_null',
				]
			);
	}

	/**
	 * Displays debug log warning
	 *
	 * @action admin_notices
	 *
	 * @return void
	 * @since  5.3.0
	 */
	public function debugWarning()
	{
		if (
			get_post_type() !== 'notification' ||
			! \Notification::component('settings')->getSetting('debugging/settings/debug_log') ||
			! \Notification::component('settings')->getSetting('debugging/settings/debug_suppressing')
		) {
			return;
		}

		$message = esc_html__(
			'Debug log is active and no notifications will be sent.',
			'notification'
		);

		$debugLogLink = sprintf(
			'<a href="%s">%s</a>',
			esc_url(admin_url('edit.php?post_type=notification&page=settings&section=debugging')),
			esc_html__('See debug log', 'notification')
		);

		echo wp_kses_post(
			sprintf(
				'<div class="notice notice-warning"><p>%s %s</p></div>',
				$message,
				$debugLogLink
			)
		);
	}

	/**
	 * Clears logs from database
	 *
	 * @action admin_post_notification_clear_logs
	 *
	 * @return void
	 * @since  6.0.0
	 */
	public function actionClearLogs()
	{
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		check_admin_referer(
			'notification_clear_log_' . wp_unslash($_GET['log_type'] ?? ''),
			'nonce'
		);

		$data = $_GET;
		$logType = $data['log_type'] ?? '';

		$debug = \Notification::component('core_debugging');

		$removeTypes = [];

		if ($logType === 'notification') {
			$removeTypes[] = 'notification';
		} elseif ($logType === 'error') {
			$removeTypes[] = 'error';
			$removeTypes[] = 'warning';
		}

		$debug->removeLogs($removeTypes);

		wp_safe_redirect(wp_get_referer());
		exit;
	}
}
