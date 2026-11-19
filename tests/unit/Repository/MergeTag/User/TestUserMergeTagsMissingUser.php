<?php

use BracketSpace\Notification\Repository\MergeTag\User\Avatar;
use BracketSpace\Notification\Repository\MergeTag\User\AvatarUrl;
use BracketSpace\Notification\Repository\MergeTag\User\UserBio;
use BracketSpace\Notification\Repository\MergeTag\User\UserDisplayName;
use BracketSpace\Notification\Repository\MergeTag\User\UserEmail;
use BracketSpace\Notification\Repository\MergeTag\User\UserFirstName;
use BracketSpace\Notification\Repository\MergeTag\User\UserID;
use BracketSpace\Notification\Repository\MergeTag\User\UserLastName;
use BracketSpace\Notification\Repository\MergeTag\User\UserLogin;
use BracketSpace\Notification\Repository\MergeTag\User\UserNicename;
use BracketSpace\Notification\Repository\MergeTag\User\UserNickname;
use BracketSpace\Notification\Repository\MergeTag\User\UserRole;
use BracketSpace\Notification\Tests\Helpers\Objects\SimpleTrigger;

/**
 * Resolves a user merge tag for the given user property value.
 *
 * @param string $class Merge tag class name.
 * @param mixed  $user  Value of the trigger's user_object property.
 * @return mixed
 */
function resolve_user_merge_tag_for_user(string $class, $user)
{
	$trigger = new class('test/trigger') extends SimpleTrigger {
		public $user_object;
	};
	$trigger->user_object = $user;

	$tag = new $class();
	$tag->setTrigger($trigger);

	return $tag->resolve();
}

/**
 * Expected value of each user merge tag for a real user, given the user object.
 *
 * @return array<class-string, callable>
 */
function user_merge_tag_expectations(): array
{
	return [
		UserID::class => fn ($user) => $user->ID,
		UserLogin::class => fn ($user) => $user->user_login,
		UserEmail::class => fn ($user) => $user->user_email,
		UserNicename::class => fn ($user) => $user->user_nicename,
		UserNickname::class => fn ($user) => $user->nickname,
		UserDisplayName::class => fn ($user) => $user->display_name,
		UserFirstName::class => fn ($user) => $user->first_name,
		UserLastName::class => fn ($user) => $user->last_name,
		UserBio::class => fn ($user) => $user->description,
		UserRole::class => fn () => 'Subscriber',
		Avatar::class => fn ($user) => get_avatar($user->user_email),
		AvatarUrl::class => fn ($user) => get_avatar_url($user->user_email),
	];
}

it('resolves user merge tags to an empty string when the user is missing', function ($class) {
	foreach ([false, null] as $missing) {
		// Integer tags (UserID) are sanitized to 0 instead of an empty string.
		expect((string) resolve_user_merge_tag_for_user($class, $missing))->toBeIn(['', '0']);
	}
})->with(array_map(fn ($class) => [$class], array_keys(user_merge_tag_expectations())));

it('resolves user merge tags for a real user', function ($class) {
	$user = get_userdata(self::factory()->user->create([
		'user_login' => 'johndoe',
		'user_email' => 'john.doe@example.com',
		'user_nicename' => 'johnny',
		'nickname' => 'jonny69',
		'display_name' => 'John Doe',
		'first_name' => 'John',
		'last_name' => 'Doe',
		'description' => 'Developer based in Ontario',
		'role' => 'subscriber',
	]));

	$expected = user_merge_tag_expectations()[$class]($user);

	expect((string) resolve_user_merge_tag_for_user($class, $user))->toBe((string) $expected);
})->with(array_map(fn ($class) => [$class], array_keys(user_merge_tag_expectations())));
