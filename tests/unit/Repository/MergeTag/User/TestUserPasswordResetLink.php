<?php
/**
 * User Password Reset Link MergeTag Test
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Repository\MergeTag\User;

use BracketSpace\Notification\Repository\MergeTag\User\UserPasswordResetLink;
use BracketSpace\Notification\Tests\Helpers\Objects\SimpleTrigger;

/**
 * User Password Reset Link test case.
 */
class TestUserPasswordResetLink extends \WP_UnitTestCase {

	/**
	 * Test password reset link encoding for various username formats
	 *
	 * @dataProvider usernameProvider
	 */
	public function test_password_reset_link_encoding($userLogin, $userEmail, $description, $expectedEncodedLogin) {
		// Create a mock user object
		$mockUser = new \stdClass();
		$mockUser->user_login = $userLogin;
		$mockUser->user_email = $userEmail;

		// Create a mock trigger object with proper property access
		$mockTrigger = new class('test/trigger') extends SimpleTrigger {
			public $user_object;
			public $password_reset_key;
		};
		$mockTrigger->user_object = $mockUser;
		$mockTrigger->password_reset_key = 'test_key_123';

		// Create the merge tag instance
		$mergeTag = new UserPasswordResetLink([
			'property_name' => 'user_object',
			'key_property_name' => 'password_reset_key'
		]);

		// Set the trigger and resolve the merge tag
		$mergeTag->setTrigger($mockTrigger);
		$url = $mergeTag->resolve();
		
		// Parse the URL to check the login parameter
		$urlParts = parse_url($url);
		
		// Check the raw query string for proper URL encoding
		// Don't use parse_str() as it decodes the values
		preg_match('/login=([^&]+)/', $urlParts['query'] ?? '', $matches);
		$actualEncodedLogin = $matches[1] ?? '';

		// Assert the login parameter is correctly encoded
		$this->assertEquals(
			$expectedEncodedLogin,
			$actualEncodedLogin,
			sprintf(
				'Failed for %s: Expected "%s" but got "%s"',
				$description,
				$expectedEncodedLogin,
				$actualEncodedLogin
			)
		);

		// Assert the URL structure is correct
		$this->assertStringContainsString('wp-login.php', $url);
		$this->assertStringContainsString('action=rp', $url);
		$this->assertStringContainsString('key=test_key_123', $url);
		$this->assertStringContainsString('login=' . $expectedEncodedLogin, $url);
	}

	/**
	 * Test fallback to user_email when user_login is empty
	 */
	public function test_fallback_to_user_email_when_login_empty() {
		// Create a mock user object with empty user_login
		$mockUser = new \stdClass();
		$mockUser->user_login = '';
		$mockUser->user_email = 'fallback@example.com';

		// Create a mock trigger object with proper property access
		$mockTrigger = new class('test/trigger') extends SimpleTrigger {
			public $user_object;
			public $password_reset_key;
		};
		$mockTrigger->user_object = $mockUser;
		$mockTrigger->password_reset_key = 'test_key_123';

		// Create the merge tag instance
		$mergeTag = new UserPasswordResetLink([
			'property_name' => 'user_object',
			'key_property_name' => 'password_reset_key'
		]);

		// Set the trigger and resolve the merge tag
		$mergeTag->setTrigger($mockTrigger);
		$url = $mergeTag->resolve();

		// Parse the URL to check the login parameter
		$urlParts = parse_url($url);
		
		// Check the raw query string for proper URL encoding
		// Don't use parse_str() as it decodes the values
		preg_match('/login=([^&]+)/', $urlParts['query'], $matches);
		$actualEncodedLogin = $matches[1] ?? '';

		// Should fallback to encoded email address
		$this->assertEquals(
			'fallback%40example.com',
			$actualEncodedLogin,
			'Should use encoded email as fallback when user_login is empty'
		);
	}

	/**
	 * Test that regular user_login is preserved when not empty
	 */
	public function test_preserves_regular_username_when_not_empty() {
		// Create a mock user object with regular username
		$mockUser = new \stdClass();
		$mockUser->user_login = 'regular_user';
		$mockUser->user_email = 'user@example.com';

		// Create a mock trigger object with proper property access
		$mockTrigger = new class('test/trigger') extends SimpleTrigger {
			public $user_object;
			public $password_reset_key;
		};
		$mockTrigger->user_object = $mockUser;
		$mockTrigger->password_reset_key = 'test_key_123';

		// Create the merge tag instance
		$mergeTag = new UserPasswordResetLink([
			'property_name' => 'user_object',
			'key_property_name' => 'password_reset_key'
		]);

		// Set the trigger and resolve the merge tag
		$mergeTag->setTrigger($mockTrigger);
		$url = $mergeTag->resolve();

		// Parse the URL to check the login parameter
		$urlParts = parse_url($url);
		
		// Check the raw query string for proper URL encoding
		// Don't use parse_str() as it decodes the values
		preg_match('/login=([^&]+)/', $urlParts['query'], $matches);
		$actualEncodedLogin = $matches[1] ?? '';

		// Should use the user_login, not fallback to email
		$this->assertEquals(
			'regular_user',
			$actualEncodedLogin,
			'Should preserve user_login when it\'s not empty'
		);
	}

	/**
	 * Data provider for various username formats
	 *
	 * @return array Test cases with [user_login, user_email, description, expected_encoded_login]
	 */
	public function usernameProvider() {
		return [
			// [user_login, user_email, description, expected_encoded_login]
			['user@example.com', 'user@example.com', 'Email with @ symbol', 'user%40example.com'],
			['name surname', 'user@example.com', 'Username with space', 'name%20surname'],
			['user name@example.com', 'user@example.com', 'Email with space before @', 'user%20name%40example.com'],
			['regular_user', 'user@example.com', 'Regular username', 'regular_user'],
			['user@domain-with-hyphens.co.uk', 'user@domain.com', 'Complex email domain', 'user%40domain-with-hyphens.co.uk'],
			['first.last+tag@example.org', 'user@example.com', 'Email with dots and plus', 'first.last%2Btag%40example.org'],
			['test@example.com', 'test@example.com', 'Standard email format', 'test%40example.com'],
			['user_123', 'user@example.com', 'Username with underscore', 'user_123'],
		];
	}
}