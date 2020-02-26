<?php
/**
 * Trigger metabox template
 *
 * @package notification
 */

?>

<h3 class="trigger-section-title"><?php esc_html_e( 'Trigger', 'notification' ); ?></h3>

<?php if ( ! $get( 'has_triggers' ) ) : ?>

	<p><?php esc_html_e( 'No Triggers defined yet', 'notification' ); ?></p>

<?php else : ?>

	<?php do_action( 'notification/metabox/trigger/before', $get( 'triggers' ), $get( 'selected' ), $get( 'notification' ) ); ?>

	<?php notification_template( 'trigger/select', $this->get_vars() ); ?>

	<?php do_action( 'notification/metabox/trigger/after', $get( 'triggers' ), $get( 'selected' ), $get( 'notification' ) ); ?>

<?php endif ?>
