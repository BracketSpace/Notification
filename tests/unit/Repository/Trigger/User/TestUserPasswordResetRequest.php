<?php
/**
 * User Password Reset Request Trigger Test
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Repository\Trigger\User;

use BracketSpace\Notification\Integration\WordPressEmails;
use BracketSpace\Notification\Repository\Trigger\User\UserPasswordResetRequest;

/**
 * User password reset request trigger test case.
 */
class TestUserPasswordResetRequest extends \WP_UnitTestCase {

	/**
	 * Context results, false means the trigger bailed
	 *
	 * @var array<mixed>
	 */
	private $results = [];

	/**
	 * Hooks the trigger the same way the plugin does
	 */
	public function set_up() {
		parent::set_up();

		$this->results = [];
		$trigger = new UserPasswordResetRequest();

		add_action(
			'retrieve_password_key',
			function ( $login, $key ) use ( $trigger ) {
				$this->results[] = $trigger->context($login, $key);
			},
			10,
			2
		);
	}

	/**
	 * Registration notification must not fire the trigger
	 */
	public function test_new_user_notification_does_not_fire() {
		$userId = wp_insert_user(
			[
				'user_login' => 'newuser',
				'user_pass' => 'secret',
				'user_email' => 'newuser@example.org',
			]
		);

		wp_new_user_notification($userId, null, 'user');

		$this->assertSame([], array_filter($this->results, static fn ( $r ) => $r !== false));
		$this->assertNotEmpty($this->results);
	}

	/**
	 * Re-sending the notification when default emails are disabled must not fire the trigger
	 */
	public function test_wordpress_emails_resend_does_not_fire() {
		$userId = $this->factory->user->create();
		$emails = new WordPressEmails();

		$emails->disableNewUserNotify($userId, 'both');

		$this->assertSame([], array_filter($this->results, static fn ( $r ) => $r !== false));
		$this->assertNotEmpty($this->results);
	}

	/**
	 * Lost password request fires the trigger
	 */
	public function test_retrieve_password_fires() {
		$this->factory->user->create(['user_login' => 'lostuser']);
		$_POST['user_login'] = 'lostuser';

		retrieve_password();

		unset($_POST['user_login']);

		$this->assertCount(1, $this->results);
		$this->assertNotFalse($this->results[0]);
	}

	/**
	 * Direct key generation (e.g. admin "Send password reset") fires the trigger
	 */
	public function test_direct_key_generation_fires() {
		$user = $this->factory->user->create_and_get();

		get_password_reset_key($user);

		$this->assertCount(1, $this->results);
		$this->assertNotFalse($this->results[0]);
	}
}
