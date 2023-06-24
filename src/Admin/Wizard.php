<?php
/**
 * Wizard class
 *
 * @package notification
 */

namespace BracketSpace\Notification\Admin;

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Core\Templates;
use BracketSpace\Notification\Core\Whitelabel;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Cache;
use BracketSpace\Notification\Dependencies\Micropackage\Cache\Driver as CacheDriver;
use BracketSpace\Notification\Dependencies\Micropackage\Filesystem\Filesystem;

/**
 * Wizard class
 */
class Wizard {

	/**
	 * Filesystem object
	 *
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * Wizard page hook.
	 *
	 * @var string
	 */
	public $page_hook = 'none';

	/**
	 * Option name for dismissed Wizard.
	 *
	 * @var string
	 */
	protected $dismissed_option = 'notification_wizard_dismissed';

	/**
	 * Wizard constructor
	 *
	 * @since 7.0.0 Changed the Files util to Filesystem.
	 * @param Filesystem $fs Includes filesystem object.
	 */
	public function __construct( Filesystem $fs ) {
		$this->filesystem = $fs;
	}

	/**
	 * Register Wizard invisible page.
	 *
	 * @action admin_menu 30
	 *
	 * @return void
	 */
	public function register_page() {
		$this->page_hook = add_submenu_page(
			'',
			__( 'Wizard', 'notification' ),
			__( 'Wizard', 'notification' ),
			'manage_options',
			'wizard',
			[ $this, 'wizard_page' ]
		);
	}

	/**
	 * Redirects the user to Wizard screen.
	 *
	 * @action current_screen
	 *
	 * @return void
	 */
	public function maybe_redirect() {
		if ( ! self::should_display() ) {
			return;
		}

		$screen = get_current_screen();

		if ( isset( $screen->post_type ) && 'notification' === $screen->post_type && 'notification_page_wizard' !== $screen->id ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=notification&page=wizard' ) );
			exit;
		}
	}

	/**
	 * Displays the Wizard page.
	 *
	 * @return void
	 */
	public function wizard_page() {
		Templates::render( 'wizard', [
			'sections' => $this->get_settings(),
		] );
	}

	/**
	 * Gets settings for Wizard page.
	 *
	 * @return array List of settings groups.
	 */
	public function get_settings() {
		return [
			[
				'name'  => __( 'Common Notifications', 'notification' ),
				'items' => [
					[
						'name'        => __( 'Post published', 'notification' ),
						'slug'        => 'post_published_admin',
						'description' => __( 'An email to administrator when post is published', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'Administrator', 'notification' ),
							],
						],
					],
					[
						'name'        => __( 'Post published', 'notification' ),
						'slug'        => 'post_published_subscribers',
						'description' => __( 'An email to all Subscribers when post is published', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'Subscribers (role)', 'notification' ),
							],
						],
					],
					[
						'name'        => __( 'Post pending review', 'notification' ),
						'slug'        => 'post_review',
						'description' => __( 'An email to administrator when post has been sent for review', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'Administrator', 'notification' ),
							],
						],
					],
					[
						'name'        => __( 'Post updated', 'notification' ),
						'slug'        => 'post_updated',
						'description' => __( 'An email to administrator when post is updated', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'Administrator', 'notification' ),
							],
						],
					],
					[
						'name'        => __( 'Welcome email', 'notification' ),
						'slug'        => 'welcome_email',
						'description' => __( 'An email to registered user', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'User', 'notification' ),
							],
						],
					],
					[
						'name'        => __( 'Comment added', 'notification' ),
						'slug'        => 'comment_added',
						'description' => __( 'An email to post author about comment to his article', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'Post author', 'notification' ),
							],
						],
					],
					[
						'name'        => __( 'Comment reply', 'notification' ),
						'slug'        => 'comment_reply',
						'description' => __( 'An email to comment autor about the reply', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'Comment author', 'notification' ),
							],
						],
					],
				],
			],
			[
				'name'  => __( 'WordPress emails', 'notification' ),
				'items' => [
					[
						'name'        => __( 'New user', 'notification' ),
						'slug'        => 'new_user',
						'description' => __( 'An email to administrator when new user is created', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'Administrator', 'notification' ),
							],
						],
					],
					[
						'name'        => __( 'Your account', 'notification' ),
						'slug'        => 'your_account',
						'description' => __( 'An email to registered user, with password reset link', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'User', 'notification' ),
							],
						],
					],
					[
						'name'        => __( 'Password reset request', 'notification' ),
						'slug'        => 'password_forgotten',
						'description' => __( 'An email to user when password reset has been requested', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'User', 'notification' ),
							],
						],
					],
					[
						'name'        => __( 'Password reset', 'notification' ),
						'slug'        => 'password_reset',
						'description' => __( 'An email with info that password has been reset', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'User', 'notification' ),
							],
						],
					],
					[
						'name'        => __( 'Comment awaiting moderation', 'notification' ),
						'slug'        => 'comment_moderation',
						'description' => __( 'An email to administrator and post author that comment is awaiting moderation', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'Administrator', 'notification' ),
							],
							[
								'name' => __( 'Post author', 'notification' ),
							],
						],
					],
					[
						'name'        => __( 'Comment has been published', 'notification' ),
						'slug'        => 'comment_published',
						'description' => __( 'An email to post author that comment has been published', 'notification' ),
						'recipients'  => [
							[
								'name' => __( 'Post author', 'notification' ),
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
	public function save_settings() {
		if ( wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ?? '' ), 'notification_wizard' ) === false ) {
			wp_die( 'Can\'t touch this' );
		}

		$data = $_POST;

		if ( ! isset( $data['skip-wizard'] ) ) {
			$notifications = isset( $data['notification_wizard'] ) ? $data['notification_wizard'] : [];
			$this->add_notifications( $notifications );
		}

		$this->save_option_to_dismiss_wizard();

		wp_safe_redirect( admin_url( 'edit.php?post_type=notification' ) );
		exit;
	}

	/**
	 * Adds predefined notifications.
	 *
	 * @action admin_post_save_notification_wizard
	 *
	 * @param  array $notifications List of notifications template slugs.
	 * @return void
	 */
	private function add_notifications( $notifications ) {
		if ( [] === $notifications ) {
			return;
		}

		$json_path_tmpl = 'resources/wizard-data/%s.json';

		foreach ( $notifications as $notification_slug ) {
			$json_path = sprintf( $json_path_tmpl, $notification_slug );

			if ( ! $this->filesystem->is_readable( $json_path ) ) {
				continue;
			}

			$json = $this->filesystem->get_contents( $json_path );

			$json_adapter = notification_adapt_from( 'JSON', $json );
			$json_adapter->refresh_hash();

			$wp_adapter = notification_swap_adapter( 'WordPress', $json_adapter );
			$wp_adapter->save();
		}

		/**
		 * @todo
		 * This cache should be cleared in Adapter save method.
		 * Now it's used in Admin\PostType::save() as well
		 */
		$cache = new CacheDriver\ObjectCache( 'notification' );
		$cache->set_key( 'notifications' );
		$cache->delete();
	}

	/**
	 * Saves option to dismiss auto-redirect to Wizard page.
	 *
	 * @return void
	 */
	private function save_option_to_dismiss_wizard() {
		if ( get_option( $this->dismissed_option ) !== false ) {
			update_option( $this->dismissed_option, true );
		} else {
			add_option( $this->dismissed_option, true, '', 'no' );
		}
	}

	/**
	 * Checks if wizard should be displayed
	 *
	 * @since  8.0.0
	 * @return bool
	 */
	public static function should_display() {
		$counter = wp_count_posts( 'notification' );
		$count   = 0;
		$count  += isset( $counter->publish ) ? $counter->publish : 0;
		$count  += isset( $counter->draft ) ? $counter->draft : 0;

		return ! Whitelabel::is_whitelabeled() && ! get_option( 'notification_wizard_dismissed' ) && ( 0 === $count );
	}

}
