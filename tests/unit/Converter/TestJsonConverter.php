<?php

use BracketSpace\Notification\Core\Notification;
use BracketSpace\Notification\Repository\Converter\JsonConverter;
use BracketSpace\Notification\Tests\Helpers\Registerer;

beforeEach(function () {
    $this->converter = new JsonConverter();
});

it('should read notification from json', function () {
	$trigger = Registerer::register_trigger();
	$carrier = Registerer::register_carrier();

	$data = [
		'hash' => uniqid(),
		'title' => uniqid(),
		'trigger' => $trigger->getSlug(),
		'carriers' => [
			$carrier->getSlug() => [
				'activated' => true,
				'enabled' => true,
			],
		],
		'enabled' => true,
		'extras' => [
			'test' => uniqid(),
		],
	];

	$notification = $this->converter->from(wp_json_encode($data));

	expect($notification)->toBeInstanceOf(Notification::class);
	expect($notification->getHash())->toEqual($data['hash']);
	expect($notification->getTitle())->toEqual($data['title']);
	expect($notification->getTrigger())->toEqual($trigger);
	expect($notification->getCarrier($carrier->getSlug()))->toBeInstanceOf(get_class($carrier));
	expect($notification->getExtras())->toEqual($data['extras']);
});

it('should save notification to json', function () {
	$notification = Registerer::register_default_notification();

	$result = $this->converter->to($notification);

	expect($result)->toBeJson();
});
