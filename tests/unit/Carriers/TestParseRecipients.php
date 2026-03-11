<?php

/**
 * Tests for BaseCarrier::parseRecipients()
 *
 * @package notification
 */

namespace BracketSpace\Notification\Tests\Carriers;

use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Repository\Carrier\Email;
use BracketSpace\Notification\Store\Recipient as RecipientStore;
use BracketSpace\Notification\Tests\Helpers\Objects\CarrierWithRecipients;
use BracketSpace\Notification\Tests\Helpers\Objects\RecipientWithReturnField;

it('clones recipients before parsing to prevent shared state mutation', function () {
	$carrier = new CarrierWithRecipients('clone_test');
	$recipient = new RecipientWithReturnField('dummy_return_field', 'user_email');

	RecipientStore::insert('clone_test', 'dummy_return_field', $recipient);

	$carrier->setRecipientsResolvedData([
		['type' => 'dummy_return_field', 'recipient' => 'test_value'],
	]);

	// Add filter that modifies the recipient it receives.
	add_filter('notification/recipient/pre_parse_value', function ($recipientObj) {
		if (method_exists($recipientObj, 'setReturnField')) {
			$recipientObj->setReturnField('ID');
		}
		return $recipientObj;
	});

	$carrier->parseRecipients();

	// The original store instance must remain unchanged.
	$storeRecipient = RecipientStore::get('clone_test', 'dummy_return_field');
	expect($storeRecipient->getReturnField())->toBe('user_email');

	remove_all_filters('notification/recipient/pre_parse_value');
});

it('sets return field on recipients supporting HasReturnField when carrier declares one', function () {
	$carrier = new class ('return_field_test') extends CarrierWithRecipients {
		protected function getRecipientReturnField(): ?string
		{
			return 'ID';
		}
	};

	$recipient = new RecipientWithReturnField('dummy_return_field', 'user_email');
	RecipientStore::insert('return_field_test', 'dummy_return_field', $recipient);

	$carrier->setRecipientsResolvedData([
		['type' => 'dummy_return_field', 'recipient' => 'val'],
	]);

	$result = $carrier->parseRecipients();

	// The carrier declares 'ID', so parsed output should use 'ID', not 'user_email'.
	expect($result)->toBe(['ID:val']);
});

it('does not override return field when carrier returns null', function () {
	$carrier = new CarrierWithRecipients('null_field_test');

	$recipient = new RecipientWithReturnField('dummy_return_field', 'ID');
	RecipientStore::insert('null_field_test', 'dummy_return_field', $recipient);

	$carrier->setRecipientsResolvedData([
		['type' => 'dummy_return_field', 'recipient' => 'val'],
	]);

	$result = $carrier->parseRecipients();

	// Carrier returns null for getRecipientReturnField, so recipient's own 'ID' should be used.
	expect($result)->toBe(['ID:val']);
});

it('fires notification/recipient/pre_parse_value filter', function () {
	$carrier = new CarrierWithRecipients('filter_test');
	$recipient = new RecipientWithReturnField('dummy_return_field', 'user_email');

	RecipientStore::insert('filter_test', 'dummy_return_field', $recipient);

	$carrier->setRecipientsResolvedData([
		['type' => 'dummy_return_field', 'recipient' => 'test_value'],
	]);

	$filterCalled = false;
	$capturedArgs = [];

	add_filter('notification/recipient/pre_parse_value', function ($recipientObj, $carrierSlug, $type) use (&$filterCalled, &$capturedArgs) {
		$filterCalled = true;
		$capturedArgs = [
			'carrier_slug' => $carrierSlug,
			'type' => $type,
		];
		return $recipientObj;
	}, 10, 3);

	$carrier->parseRecipients();

	expect($filterCalled)->toBeTrue();
	expect($capturedArgs['carrier_slug'])->toBe('filter_test');
	expect($capturedArgs['type'])->toBe('dummy_return_field');

	remove_all_filters('notification/recipient/pre_parse_value');
});

it('returns user_email from getRecipientReturnField for Email carrier', function () {
	$email = new Email();

	$reflection = new \ReflectionMethod($email, 'getRecipientReturnField');
	$reflection->setAccessible(true);

	expect($reflection->invoke($email))->toBe('user_email');
});

it('returns null from getRecipientReturnField by default', function () {
	$carrier = new CarrierWithRecipients();

	$reflection = new \ReflectionMethod($carrier, 'getRecipientReturnField');
	$reflection->setAccessible(true);

	expect($reflection->invoke($carrier))->toBeNull();
});
