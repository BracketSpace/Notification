<?php

use BracketSpace\Notification\Admin\PostTable;
use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Repository\Recipient;
use BracketSpace\Notification\Store\Recipient as RecipientStore;
use BracketSpace\Notification\Tests\Helpers\Objects\CarrierWithRecipients;

/**
 * Builds a notification with an enabled carrier holding given recipients.
 *
 * @param array<array<string,string>> $recipients Recipients rows.
 * @param string $slug Carrier slug.
 */
function notificationWithRecipients(array $recipients, string $slug = 'rc_test'): Notification
{
	$carrier = new CarrierWithRecipients($slug);

	foreach ([new Recipient\Email(), new Recipient\Role(), new Recipient\UserID(), new Recipient\Administrator()] as $recipient) {
		RecipientStore::insert($slug, $recipient->getSlug(), $recipient);
	}

	$carrier->enable();
	$carrier->setData(['recipients' => $recipients]);

	return new Notification(['enabled' => true, 'carriers' => [$carrier]]);
}

it('registers the recipients column before the date column', function () {
	$columns = (new PostTable())->tableColumns(['cb' => '', 'title' => 'Title', 'date' => 'Date']);

	expect($columns)->toHaveKey('recipients');
	expect(array_keys($columns))->toBe(['cb', 'switch', 'title', 'hash', 'trigger', 'carriers', 'recipients', 'date']);
	expect(apply_filters('notification/admin/allow_column/recipients', false))->toBeTrue();
});

it('renders recipients of different types human readably', function () {
	$userId = $this->factory()->user->create(['display_name' => 'John Doe']);

	$notification = notificationWithRecipients([
		['type' => 'email', 'recipient' => 'a@b.com'],
		['type' => 'email', 'recipient' => '{user_email}'],
		['type' => 'role', 'recipient' => 'editor'],
		['type' => 'user_id', 'recipient' => (string)$userId],
		['type' => 'user_id', 'recipient' => '{post_author_ID}'],
		['type' => 'administrator', 'recipient' => 'admin@example.com'],
	]);

	expect((new PostTable())->recipientsSummary($notification))
		->toBe('a@b.com, {user_email}, Role: Editor, User: John Doe, User: {post_author_ID} +1 more');
});

it('escapes recipient values', function () {
	$notification = notificationWithRecipients([['type' => 'email', 'recipient' => 'a&b@c.com']]);

	expect((new PostTable())->recipientsSummary($notification))->toBe('a&amp;b@c.com');
});

it('prefixes recipients with carrier name when multiple carriers have them', function () {
	$one = notificationWithRecipients([['type' => 'email', 'recipient' => 'a@b.com']], 'rc_one');
	$two = notificationWithRecipients([['type' => 'administrator', 'recipient' => '']], 'rc_two');
	$notification = new Notification([
		'enabled' => true,
		'carriers' => [$one->getCarrier('rc_one'), $two->getCarrier('rc_two')],
	]);

	expect((new PostTable())->recipientsSummary($notification))
		->toBe('<strong>Test Carrier:</strong> a@b.com<br><strong>Test Carrier:</strong> Administrator');
});

it('renders nothing when there are no recipients', function () {
	expect((new PostTable())->recipientsSummary(notificationWithRecipients([])))->toBe('');
});
