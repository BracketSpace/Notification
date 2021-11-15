<?php
/**
 * Trigger metabox template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var callable(string $var_name, string $default=): void $the_esc Escaped variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

use BracketSpace\Notification\Core\Templates;

?>

<h3 class="trigger-section-title"><?php esc_html_e( 'Trigger', 'notification' ); ?></h3>

<?php if ( ! $get( 'has_triggers' ) ) : ?>

	<p><?php esc_html_e( 'No Triggers defined yet', 'notification' ); ?></p>

<?php else : ?>

	<?php do_action( 'notification/metabox/trigger/before', $get( 'triggers' ), $get( 'selected' ), $get( 'notification' ) ); ?>

	<?php Templates::render( 'trigger/select', $this->get_vars() ); ?>

	<?php do_action( 'notification/metabox/trigger/after', $get( 'triggers' ), $get( 'selected' ), $get( 'notification' ) ); ?>

<?php endif ?>
