<?php

/**
 * Wizard class
 *
 * @package notification
 */

declare(strict_types=1);

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Core\Whitelabel;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;
use BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem;

/**
 * Wizard class
 */
class Wizard
{
	/**
	 * Filesystem object
	 *
	 * @var \BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * Wizard page hook.
	 *
	 * @var string|false
	 */
	public $pageHook = 'none';

	/**
	 * Option name for dismissed Wizard.
	 *
	 * @var string
	 */
	protected $dismissedOption = 'notification_wizard_dismissed';

	/**
	 * Wizard constructor
	 *
	 * @param \BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem $fs Includes filesystem object.
	 * @since 7.0.0 Changed the Files util to Filesystem.
	 */
	public function __construct(Filesystem $fs)
	{
		$this->filesystem = $fs;
	}

	/**
	 * Register Wizard invisible page.
	 *
	 * @action admin_menu 30
	 *
	 * @return void
	 */
	public function registerPage()
	{
		$this->pageHook = add_submenu_page(
			'',
			__(
				'Wizard',
				'notification'
			),
			__(
				'Wizard',
				'notification'
			),
			'manage_options',
			'wizard',
			[$this, 'wizardPage']
		);
	}

	/**
	 * Redirects the user to Wizard screen.
	 *
	 * @action current_screen
	 *
	 * @return void
	 */
	public function maybeRedirect()
	{
		if (!self::shouldDisplay()) {
			return;
		}

		$screen = get_current_screen();

		if (
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			isset($screen->post_type) &&
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			$screen->post_type === 'notification' &&
			$screen->id !== 'notification_page_wizard'
		) {
			wp_safe_redirect(admin_url('edit.php?post_type=notification&page=wizard'));
			exit;
		}
	}

	/**
	 * Displays the Wizard page.
	 *
	 * @return void
	 */
	public function wizardPage()
	{
		Templates::render(
			'wizard',
			[
				'sections' => $this->getSettings(),
			]
		);
	}

	/**
	 * Gets settings for Wizard page.
	 *
	 * @return array<mixed> List of settings groups.
	 */
	public function getSettings()
	{
		return [
			[
				'name' => __(
					'Common Notifications',
					'notification'
				),
				'items' => [
					[
						'name' => __(
							'Post published',
							'notification'
						),
						'slug' => 'post_published_admin',
						'description' => __(
							'An email to administrator when post is published',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'Administrator',
									'notification'
								),
							],
						],
					],
					[
						'name' => __(
							'Post published',
							'notification'
						),
						'slug' => 'post_published_subscribers',
						'description' => __(
							'An email to all Subscribers when post is published',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'Subscribers (role)',
									'notification'
								),
							],
						],
					],
					[
						'name' => __(
							'Post pending review',
							'notification'
						),
						'slug' => 'post_review',
						'description' => __(
							'An email to administrator when post has been sent for review',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'Administrator',
									'notification'
								),
							],
						],
					],
					[
						'name' => __(
							'Post updated',
							'notification'
						),
						'slug' => 'post_updated',
						'description' => __(
							'An email to administrator when post is updated',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'Administrator',
									'notification'
								),
							],
						],
					],
					[
						'name' => __(
							'Welcome email',
							'notification'
						),
						'slug' => 'welcome_email',
						'description' => __(
							'An email to registered user',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'User',
									'notification'
								),
							],
						],
					],
					[
						'name' => __(
							'Comment added',
							'notification'
						),
						'slug' => 'comment_added',
						'description' => __(
							'An email to post author about comment to his article',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'Post author',
									'notification'
								),
							],
						],
					],
					[
						'name' => __(
							'Comment reply',
							'notification'
						),
						'slug' => 'comment_reply',
						'description' => __(
							'An email to comment autor about the reply',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'Comment author',
									'notification'
								),
							],
						],
					],
				],
			],
			[
				'name' => __(
					'WordPress emails',
					'notification'
				),
				'items' => [
					[
						'name' => __(
							'New user',
							'notification'
						),
						'slug' => 'new_user',
						'description' => __(
							'An email to administrator when new user is created',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'Administrator',
									'notification'
								),
							],
						],
					],
					[
						'name' => __(
							'Your account',
							'notification'
						),
						'slug' => 'your_account',
						'description' => __(
							'An email to registered user, with password reset link',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'User',
									'notification'
								),
							],
						],
					],
					[
						'name' => __(
							'Password reset request',
							'notification'
						),
						'slug' => 'password_forgotten',
						'description' => __(
							'An email to user when password reset has been requested',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'User',
									'notification'
								),
							],
						],
					],
					[
						'name' => __(
							'Password reset',
							'notification'
						),
						'slug' => 'password_reset',
						'description' => __(
							'An email with info that password has been reset',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'User',
									'notification'
								),
							],
						],
					],
					[
						'name' => __(
							'Comment awaiting moderation',
							'notification'
						),
						'slug' => 'comment_moderation',
						'description' => __(
							'An email to administrator and post author that comment is awaiting moderation',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'Administrator',
									'notification'
								),
							],
							[
								'name' => __(
									'Post author',
									'notification'
								),
							],
						],
					],
					[
						'name' => __(
							'Comment has been published',
							'notification'
						),
						'slug' => 'comment_published',
						'description' => __(
							'An email to post author that comment has been published',
							'notification'
						),
						'recipients' => [
							[
								'name' => __(
									'Post author',
									'notification'
								),
							],
						],
					],
				],
			],
		];
	}

	/**
	 * Saves Wizard settings.
	 *
	 * @action admin_post_save_notification_wizard
	 *
	 * @return void
	 */
	public function saveSettings()
	{
		if (
			wp_verify_nonce(
				sanitize_key($_POST['_wpnonce'] ?? ''),
				'notification_wizard'
			) === false
		) {
			wp_die('Can\'t touch this');
		}

		$data = $_POST;

		if (!isset($data['skip-wizard'])) {
			$notifications = $data['notification_wizard'] ?? [];
			$this->addNotifications($notifications);
		}

		$this->saveOptionToDismissWizard();

		wp_safe_redirect(admin_url('edit.php?post_type=notification'));
		exit;
	}

	/**
	 * Adds predefined notifications.
	 *
	 * @action admin_post_save_notification_wizard
	 *
	 * @param array<mixed> $notifications List of notifications template slugs.
	 * @return void
	 */
	private function addNotifications($notifications)
	{
		if ($notifications === []) {
			return;
		}

		$jsonPathTmpl = 'resources/wizard-data/%s.json';

		foreach ($notifications as $notificationSlug) {
			$jsonPath = sprintf(
				$jsonPathTmpl,
				$notificationSlug
			);

			if (!$this->filesystem->is_readable($jsonPath)) {
				continue;
			}

			$json = $this->filesystem->get_contents($jsonPath);

			$jsonAdapter = \BracketSpace\Notification\adaptFrom(
				'JSON',
				$json
			);
			$jsonAdapter->refreshHash();

			$wpAdapter = \BracketSpace\Notification\swapAdapter(
				'WordPress',
				$jsonAdapter
			);
			$wpAdapter->save();
		}

		/**
		 * @todo
		 * This cache should be cleared in Adapter save method.
		 * Now it's used in Admin\PostType::save() as well
		 */
		$cache = new CacheDriver\ObjectCache('notification');
		$cache->set_key('notifications');
		$cache->delete();
	}

	/**
	 * Saves option to dismiss auto-redirect to Wizard page.
	 *
	 * @return void
	 */
	private function saveOptionToDismissWizard()
	{
		if (get_option($this->dismissedOption) !== false) {
			update_option(
				$this->dismissedOption,
				true
			);
		} else {
			add_option(
				$this->dismissedOption,
				true,
				'',
				'no'
			);
		}
	}

	/**
	 * Checks if wizard should be displayed
	 *
	 * @return bool
	 * @since  8.0.0
	 */
	public static function shouldDisplay()
	{
		/** @var array{publish: int, draft: int} $counter */
		$counter = (array)wp_count_posts('notification');
		$count = 0;

		$count += $counter['publish'] ?? 0;
		$count += $counter['draft'] ?? 0;

		return !Whitelabel::isWhitelabeled() && !get_option('notification_wizard_dismissed') && ($count === 0);
	}
}
