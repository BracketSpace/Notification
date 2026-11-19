<?php
/**
 * User Login Failed Trigger Test
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Repository\Trigger\User;

use BracketSpace\Notification\Repository\MergeTag\User\UserEmail;
use BracketSpace\Notification\Repository\Trigger\User\UserLoginFailed;

/**
 * User Login Failed trigger test case.
 */
class TestUserLoginFailed extends \WP_UnitTestCase {

	/**
	 * Context sets user data for an existing user
	 */
	public function test_context_sets_user() {
		$userId = $this->factory->user->create([
			'user_login' => 'failed_user',
			'user_email' => 'failed@example.com',
		]);
		$trigger = new UserLoginFailed();

		$result = $trigger->context('failed_user');

		$this->assertNotFalse($result);
		$this->assertSame($userId, $trigger->userId);
		$this->assertSame('failed_user', $trigger->userObject->user_login);

		$tag = new UserEmail();
		$tag->setTrigger($trigger);
		$this->assertSame('failed@example.com', $tag->resolve());
	}

	/**
	 * Unknown users do not fire the trigger
	 */
	public function test_context_bails_for_unknown_user() {
		$this->assertFalse((new UserLoginFailed())->context('no_such_user'));
	}
}
