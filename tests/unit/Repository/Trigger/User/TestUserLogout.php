<?php
/**
 * User Logout Trigger Test
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Repository\Trigger\User;

use BracketSpace\Notification\Repository\MergeTag\User\UserLogin;
use BracketSpace\Notification\Repository\Trigger\User\UserLogout;

/**
 * User Logout trigger test case.
 */
class TestUserLogout extends \WP_UnitTestCase {

	/**
	 * Context is built from the hook argument
	 */
	public function test_context_uses_passed_user_id() {
		$userId = $this->factory->user->create(['user_login' => 'logout_user']);
		$trigger = new UserLogout();

		$trigger->context($userId);

		$this->assertSame($userId, $trigger->userId);
		$this->assertInstanceOf(\WP_User::class, $trigger->userObject);
		$this->assertSame('logout_user', $trigger->userObject->user_login);

		$tag = new UserLogin();
		$tag->setTrigger($trigger);
		$this->assertSame('logout_user', $tag->resolve());
	}

	/**
	 * Falls back to the current user when the argument is missing
	 */
	public function test_context_falls_back_to_current_user() {
		$userId = $this->factory->user->create();
		wp_set_current_user($userId);
		$trigger = new UserLogout();

		$trigger->context();

		$this->assertSame($userId, $trigger->userId);
		$this->assertSame($userId, $trigger->userObject->ID);
	}
}
