<?php
/**
 * User Registered Trigger Test
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Repository\Trigger\User;

use BracketSpace\Notification\Repository\Trigger\User\UserRegistered;

/**
 * User Registered trigger test case.
 */
class TestUserRegistered extends \WP_UnitTestCase {

	/**
	 * Test password setup link encoding for various username formats
	 *
	 * @dataProvider usernameProvider
	 */
	public function test_password_setup_link_encoding($userLogin, $userEmail, $description, $expectedEncodedLogin) {
		// Create a mock user object
		$mockUser = new \stdClass();
		$mockUser->user_login = $userLogin;
		$mockUser->user_email = $userEmail;
		$mockUser->ID = 123;

		// Create the trigger instance
		$trigger = new UserRegistered();
		$trigger->userObject = $mockUser;

		// Mock the getPasswordResetKey method
		$trigger = $this->getMockBuilder(UserRegistered::class)
			->onlyMethods(['getPasswordResetKey'])
			->getMock();
		
		$trigger->method('getPasswordResetKey')
			->willReturn('test_key_123');
		
		$trigger->userObject = $mockUser;

		// Get the merge tags
		$trigger->mergeTags();
		$mergeTags = $trigger->get_merge_tags();

		// Find the password setup link merge tag
		$passwordSetupTag = null;
		foreach ($mergeTags as $tag) {
			if ($tag->getSlug() === 'user_password_setup_link') {
				$passwordSetupTag = $tag;
				break;
			}
		}

		$this->assertNotNull($passwordSetupTag, 'Password setup link merge tag should exist');

		// Set the trigger and resolve the merge tag
		$passwordSetupTag->setTrigger($trigger);
		$url = $passwordSetupTag->resolve();

		// Parse the URL to check the login parameter
		$urlParts = parse_url($url);
		
		// Check the raw query string for proper URL encoding
		// Don't use parse_str() as it decodes the values
		preg_match('/login=([^&]+)/', $urlParts['query'], $matches);
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
	public function test_password_setup_fallback_to_user_email() {
		// Create a mock user object with empty user_login
		$mockUser = new \stdClass();
		$mockUser->user_login = '';
		$mockUser->user_email = 'fallback@example.com';
		$mockUser->ID = 123;

		// Create the trigger instance
		$trigger = $this->getMockBuilder(UserRegistered::class)
			->onlyMethods(['getPasswordResetKey'])
			->getMock();
		
		$trigger->method('getPasswordResetKey')
			->willReturn('test_key_123');
		
		$trigger->userObject = $mockUser;

		// Get the merge tags
		$trigger->mergeTags();
		$mergeTags = $trigger->get_merge_tags();

		// Find the password setup link merge tag
		$passwordSetupTag = null;
		foreach ($mergeTags as $tag) {
			if ($tag->getSlug() === 'user_password_setup_link') {
				$passwordSetupTag = $tag;
				break;
			}
		}

		$this->assertNotNull($passwordSetupTag, 'Password setup link merge tag should exist');

		// Set the trigger and resolve the merge tag
		$passwordSetupTag->setTrigger($trigger);
		$url = $passwordSetupTag->resolve();

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
			['user@domain-with-hyphens.co.uk', 'user@example.com', 'Complex email domain', 'user%40domain-with-hyphens.co.uk'],
			['first.last+tag@example.org', 'user@example.com', 'Email with dots and plus', 'first.last%2Btag%40example.org'],
			['test@example.com', 'test@example.com', 'Standard email format', 'test%40example.com'],
			['user_123', 'user@example.com', 'Username with underscore', 'user_123'],
		];
	}
}