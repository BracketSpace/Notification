<?php

use BracketSpace\Notification\Tests\Helpers\Registerer;

require_once 'bootstrap.php';

uses(Tests\UnitTestCase::class)
	->beforeAll(function () {
		Notification::component('core_upgrade')->upgradeDb();
	})
	->afterEach(function () {
		Registerer::clear();
	})
	->group('unit')
	->in('unit');
