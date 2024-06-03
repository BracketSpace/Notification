<?php

use BracketSpace\Notification\Core\Upgrade;
use BracketSpace\Notification\Tests\Helpers\Registerer;

require_once 'bootstrap.php';

uses(Tests\UnitTestCase::class)
	->beforeAll(function () {
		Notification::component(Upgrade::class)->upgradeDb();
	})
	->afterEach(function () {
		Registerer::clear();
	})
	->group('unit')
	->in('unit');
