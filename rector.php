<?php

use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\DowngradeSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
	$parameters = $containerConfigurator->parameters();
	$parameters->set(Option::SETS, [
		DowngradeSetList::PHP_74,
		DowngradeSetList::PHP_73,
		DowngradeSetList::PHP_72,
		DowngradeSetList::PHP_71,
	]);
	$parameters->set(Option::PATHS, [
		__DIR__ . '/src',
		__DIR__ . '/tests',
	]);
	$parameters->set(Option::SKIP, [
		__DIR__ . '/tests/_wordpress',
	]);
};
