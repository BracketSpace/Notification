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

	// Classes meant to be left as is, ie. deprecated ones.
	$exclusions = [
		'BracketSpace\Notification\Defaults\Adapter',
	];

	foreach ($deprecations as $version => $map) {
		foreach ($map as $oldNamespace => $newNamespace) {
			// Match the loaded classname.
			if (strpos($class, $oldNamespace) !== 0) {
				continue;
			}

			// Check for exclusions.
			foreach ($exclusions as $excludedNamespace) {
				if (strpos($class, $excludedNamespace) !== 0) {
					continue;
				}
			}

			$newClass = str_replace($oldNamespace, $newNamespace, $class);

			notification_deprecated_class($class, $version, $newClass);

			class_alias($newClass, $class);
		}
	}
});
