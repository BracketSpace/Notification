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
			'BracketSpace\Notification\Abstracts\Carrier' => 'BracketSpace\Notification\Repository\Carrier\BaseCarrier',
			'BracketSpace\Notification\Abstracts\Field' => 'BracketSpace\Notification\Repository\Field\BaseField',
			'BracketSpace\Notification\Abstracts\MergeTag' => 'BracketSpace\Notification\Repository\MergeTag\BaseMergeTag',
			'BracketSpace\Notification\Abstracts\Recipient' => 'BracketSpace\Notification\Repository\Recipient\BaseRecipient',
			'BracketSpace\Notification\Abstracts\Resolver' => 'BracketSpace\Notification\Repository\Resolver\BaseResolver',
			'BracketSpace\Notification\Abstracts\Trigger' => 'BracketSpace\Notification\Repository\Trigger\BaseTrigger',
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
				if (strpos($class, $excludedNamespace) === 0) {
					break 2;
				}
			}

			$newClass = str_replace($oldNamespace, $newNamespace, $class);

			if (! class_exists($newClass)) {
				break;
			}

			notification_deprecated_class($class, $version, $newClass);

			class_alias($newClass, $class);
		}
	}
});
