<?php
/**
 * User Password Reset Request Trigger Test
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Repository\Trigger\User;

use BracketSpace\Notification\Repository\Trigger\User\UserPasswordResetRequest;

/**
 * User Password Reset Request trigger test case.
 */
class TestUserPasswordResetRequest extends \WP_UnitTestCase {

	/**
	 * Setup test
	 */
	public function setUp(): void {
		parent::setUp();
	}

	/**
	 * Test user retrieval by login
	 */
	public function test_user_retrieval_by_login() {
		// Create a test user with regular username
		$userId = $this->factory->user->create([
			'user_login' => 'test_user',
			'user_email' => 'testuser@example.com'
		]);

		// Create the trigger instance
		$trigger = new UserPasswordResetRequest();

		// Test context method with username
		$result = $trigger->context('test_user', 'test_reset_key');

		// Should not return false (user should be found)
		$this->assertNotFalse($result, 'User should be found by login');
		$this->assertEquals($userId, $trigger->userId, 'User ID should match');
		$this->assertEquals('test_user', $trigger->userObject->user_login, 'User login should match');
		$this->assertEquals('test_reset_key', $trigger->passwordResetKey, 'Reset key should be stored');
	}

	/**
	 * Test user retrieval by email (fallback)
	 */
	public function test_user_retrieval_by_email_fallback() {
		// Create a test user with email as login
		$userId = $this->factory->user->create([
			'user_login' => 'test_email_user',
			'user_email' => 'emailuser@example.com'
		]);

		// Create the trigger instance
		$trigger = new UserPasswordResetRequest();

		// Test context method with email address (should fallback to email search)
		$result = $trigger->context('emailuser@example.com', 'test_reset_key');

		// Should not return false (user should be found by email fallback)
		$this->assertNotFalse($result, 'User should be found by email fallback');
		$this->assertEquals($userId, $trigger->userId, 'User ID should match');
		$this->assertEquals('test_email_user', $trigger->userObject->user_login, 'User login should match');
		$this->assertEquals('emailuser@example.com', $trigger->userObject->user_email, 'User email should match');
		$this->assertEquals('test_reset_key', $trigger->passwordResetKey, 'Reset key should be stored');
	}

	/**
	 * Test user retrieval fails when neither login nor email matches
	 */
	public function test_user_retrieval_fails_when_not_found() {
		// Create the trigger instance
		$trigger = new UserPasswordResetRequest();

		// Test context method with non-existent username
		$result = $trigger->context('nonexistent_user', 'test_reset_key');

		// Should return false (user not found)
		$this->assertFalse($result, 'Should return false when user not found');
	}

	/**
	 * Test user retrieval fails with invalid email format
	 */
	public function test_user_retrieval_fails_with_invalid_email() {
		// Create the trigger instance
		$trigger = new UserPasswordResetRequest();

		// Test context method with invalid email format
		$result = $trigger->context('invalid-email-format', 'test_reset_key');

		// Should return false (not a valid email, and user doesn't exist)
		$this->assertFalse($result, 'Should return false with invalid email format');
	}

	/**
	 * Test user retrieval with email address containing special characters
	 */
	public function test_user_retrieval_with_complex_email() {
		// Create a test user with complex email
		$complexEmail = 'user.name+tag@sub-domain.example.com';
		$userId = $this->factory->user->create([
			'user_login' => 'complex_user',
			'user_email' => $complexEmail
		]);

		// Create the trigger instance
		$trigger = new UserPasswordResetRequest();

		// Test context method with complex email
		$result = $trigger->context($complexEmail, 'test_reset_key');

		// Should not return false (user should be found by email)
		$this->assertNotFalse($result, 'User should be found with complex email');
		$this->assertEquals($userId, $trigger->userId, 'User ID should match');
		$this->assertEquals($complexEmail, $trigger->userObject->user_email, 'User email should match');
		$this->assertEquals('test_reset_key', $trigger->passwordResetKey, 'Reset key should be stored');
	}

	/**
	 * Test that login search is tried first before email fallback
	 */
	public function test_login_search_priority_over_email() {
		// Create two users: one with email as login, another with that email as email address
		$user1Id = $this->factory->user->create([
			'user_login' => 'priority@example.com', // This user has email as login
			'user_email' => 'different@example.com'
		]);

		$user2Id = $this->factory->user->create([
			'user_login' => 'other_user',
			'user_email' => 'priority@example.com' // This user has the email as email address
		]);

		// Create the trigger instance
		$trigger = new UserPasswordResetRequest();

		// Test context method - should find the first user (by login)
		$result = $trigger->context('priority@example.com', 'test_reset_key');

		// Should find the user by login (user1), not by email (user2)
		$this->assertNotFalse($result, 'User should be found');
		$this->assertEquals($user1Id, $trigger->userId, 'Should find user by login first, not email');
		$this->assertEquals('priority@example.com', $trigger->userObject->user_login, 'Should match login');
	}
}