<?php
/**
 * Trigger metabox template
 *
 * @package notification
 */

?>

<h3 class="trigger-section-title"><?php esc_html_e( 'Trigger', 'notification' ); ?></h3>

<?php if ( ! $this->get_var( 'has_triggers' ) ) : ?>

	<p><?php esc_html_e( 'No Triggers defined yet', 'notification' ); ?></p>

<?php else : ?>

	<?php do_action( 'notification/metabox/trigger/before', $this->get_var( 'triggers' ), $this->get_var( 'selected' ), $this->get_var( 'notification' ) ); ?>

	<?php $this->get_view( 'trigger/select' ); ?>

	<?php do_action( 'notification/metabox/trigger/after', $this->get_var( 'triggers' ), $this->get_var( 'selected' ), $this->get_var( 'notification' ) ); ?>

<?php endif ?>
