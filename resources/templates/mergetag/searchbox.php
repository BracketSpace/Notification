<?php
/**
 * Merge tag searchbox template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>

<input type="text" placeholder="<?php esc_attr_e( 'Search merge tags', 'notification' ); ?>" class="widefat notification-search-merge-tags" autocomplete="off" id="notification-search-merge-tags">
