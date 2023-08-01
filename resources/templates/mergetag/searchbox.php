<?php

declare(strict_types=1);

/**
 * Merge tag searchbox template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $the_esc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>

<input
	type="text"
	placeholder="<?php esc_attr_e('Search merge tags', 'notification'); ?>"
	class="widefat notification-search-merge-tags"
	autocomplete="off"
	id="notification-search-merge-tags"
>
