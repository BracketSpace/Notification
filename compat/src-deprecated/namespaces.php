<?php
/**
 * Deprecated namespaces
 *
 * @package notification
 */

spl_autoload_register(function ($class) {
	$deprecations = [
		'[Next]' => [
			'BracketSpace\Notification\Defaults' => 'BracketSpace\Notification\Repository',
		],
	];

	foreach ($deprecations as $version => $map) {
		foreach ($map as $oldNamespace => $newNamespace) {
			if (strpos($class, $oldNamespace) !== 0) {
				continue;
			}

			$newClass = str_replace($oldNamespace, $newNamespace, $class);

			notification_deprecated_class($class, $version, $newClass);

			class_alias($newClass, $class);
		}
	}
});
