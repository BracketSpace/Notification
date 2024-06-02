<?php

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Database\DatabaseService;
use BracketSpace\Notification\Database\NotificationDatabaseService;
use BracketSpace\Notification\Tests\Helpers\Registerer;

function upsertAndGet($notification)
{
	NotificationDatabaseService::upsert($notification);
	return NotificationDatabaseService::get($notification->getHash());
}

it('should not have any saved notifications by default', function () {
	expect(NotificationDatabaseService::count())->toBe(0);
});

it('should get null if notification is not saved', function () {
	expect(NotificationDatabaseService::get('dummy'))->toBeNull();
});

it('should store notification', function () {
	expect(NotificationDatabaseService::count())->toBe(0);

	$notification = Registerer::register_default_notification();
	$notificationFromDb = upsertAndGet($notification);

	expect(NotificationDatabaseService::count())->toBe(1);

	expect($notificationFromDb)->toBeInstanceOf(Notification::class);
	expect($notificationFromDb)->toEqual($notification);
});

it('should check if notification exists', function () {
	expect(NotificationDatabaseService::count())->toBe(0);

	$notification = Registerer::register_default_notification();

	expect(NotificationDatabaseService::has($notification->getHash()))->toBeFalse();

	NotificationDatabaseService::upsert($notification);

	expect(NotificationDatabaseService::has($notification->getHash()))->toBeTrue();

	NotificationDatabaseService::delete($notification->getHash());

	expect(NotificationDatabaseService::has($notification->getHash()))->toBeFalse();
});

it('should delete notification', function () {
	expect(NotificationDatabaseService::count())->toBe(0);

	$notification = Registerer::register_default_notification();

	NotificationDatabaseService::upsert($notification);

	expect(NotificationDatabaseService::count())->toBe(1);

	NotificationDatabaseService::delete($notification->getHash());

	expect(NotificationDatabaseService::get($notification->getHash()))->toBeNull();
	expect(NotificationDatabaseService::count())->toBe(0);

	$carriersDataRaw = DatabaseService::db()->get_results(
		DatabaseService::db()->prepare(
			'SELECT * FROM %i WHERE notification_hash = %s',
			NotificationDatabaseService::getNotificationCarriersTableName(),
			$notification->getHash()
		),
		'ARRAY_A'
	);

	$extrasDataRaw = DatabaseService::db()->get_results(
		DatabaseService::db()->prepare(
			'SELECT * FROM %i WHERE notification_hash = %s',
			NotificationDatabaseService::getNotificationExtrasTableName(),
			$notification->getHash()
		),
		'ARRAY_A'
	);

	expect(count($carriersDataRaw))->toBe(0);
	expect(count($extrasDataRaw))->toBe(0);
});

it('should update notification data', function () {
	$notification = Registerer::register_default_notification();

	NotificationDatabaseService::upsert($notification);

	$updatedTitle = uniqid(true);
	$updatedTrigger = Registerer::register_trigger(uniqid(true));

	$notification->setTitle($updatedTitle);
	$notification->setTrigger($updatedTrigger);
	$notification->setEnabled(false);

	$notificationFromDb = upsertAndGet($notification);

	expect($notificationFromDb->getTitle())->toEqual($updatedTitle);
	expect($notificationFromDb->getTrigger())->toEqual($updatedTrigger);
	expect($notificationFromDb->getEnabled())->toBeFalse();
});

it('should update notification carriers', function () {
	$notification = Registerer::register_default_notification();
	$updatedCarriers = $notification->getCarriers();

	// Set carriers to none.
	$notification->setCarriers([]);
	$notificationFromDb = upsertAndGet($notification);
	expect($notificationFromDb->getCarriers())->toBe([]);

	// Add a carrier
	$notification->setCarriers($updatedCarriers);
	$notificationFromDb = upsertAndGet($notification);
	expect($notificationFromDb->getCarriers())->toEqual($updatedCarriers);

	// Update carrier data
	$notification->getCarrier(reset($updatedCarriers)->getSlug())->deactivate();
	$notificationFromDb = upsertAndGet($notification);
	expect($notificationFromDb->getCarriers())->toEqual($notification->getCarriers());

	// Set carriers back to none.
	$notification->setCarriers([]);
	$notificationFromDb = upsertAndGet($notification);
	expect($notificationFromDb->getCarriers())->toBe([]);
});

it('should update notification extras', function () {
	$notification = Registerer::register_default_notification();

	// Should have no extras
	$notificationFromDb = upsertAndGet($notification);
	expect($notificationFromDb->getExtras())->toBe([]);

	// Add extra
	$notification->addExtra('extra', uniqid(true));
	$notificationFromDb = upsertAndGet($notification);
	expect($notificationFromDb->getExtras())->toEqual($notification->getExtras());

	// Add one more
	$notification->addExtra('new', uniqid(true));
	$notificationFromDb = upsertAndGet($notification);
	expect($notificationFromDb->getExtras())->toEqual($notification->getExtras());

	// Update old extra
	$notification->addExtra('extra', 'new value');
	$notificationFromDb = upsertAndGet($notification);
	expect($notificationFromDb->getExtras())->toEqual($notification->getExtras());

	// Remove old extra
	$notification->removeExtra('extra');
	$notificationFromDb = upsertAndGet($notification);
	expect($notificationFromDb->getExtras())->toEqual($notification->getExtras());

	// Remove all extras
	$notification->setExtras([]);
	$notificationFromDb = upsertAndGet($notification);
	expect($notificationFromDb->getExtras())->toEqual($notification->getExtras());
});

it('should get all stored notifications', function ($number) {
	expect(NotificationDatabaseService::count())->toBe(0);

	$notifications = [];

	for ($i=0; $i < $number; $i++) {
		$n = Registerer::register_default_notification();
		$notifications[$n->getHash()] = $n;
		NotificationDatabaseService::upsert($n);
	}

	expect(NotificationDatabaseService::count())->toBe($number);
	expect(NotificationDatabaseService::getAll())->toEqual($notifications);
})->with([
    'none' => 0,
    'one' => 1,
    'random' => rand(2, 20),
]);
