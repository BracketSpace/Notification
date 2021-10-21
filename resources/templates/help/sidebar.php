<?php
/**
 * Screen help sidebar template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>

<h3><?php esc_html_e( 'Useful links', 'notification' ); ?></h3>

<ul>
	<li><a href="https://wordpress.org/support/plugin/notification" target="_blank"><?php esc_html_e( 'Support', 'notification' ); ?></a></li>
	<li><a href="https://docs.bracketspace.com/notification/" target="_blank"><?php esc_html_e( 'Documentation', 'notification' ); ?></a></li>
	<li><a href="https://wordpress.org/support/plugin/notification/reviews/#new-post" target="_blank"><?php esc_html_e( 'Rate the plugin', 'notification' ); ?></a></li>
</ul>
