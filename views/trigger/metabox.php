<?php
/**
 * Trigger metabox template
 *
 * @package notification
 */

?>

<?php if ( ! $this->get_var( 'has_triggers' ) ) : ?>

	<p><?php esc_html_e( 'No Triggers defined yet', 'notification' ); ?></p>

<?php else : ?>

	<?php wp_nonce_field( 'notification_trigger', 'trigger_nonce' ); ?>

	<?php do_action( 'notification/metabox/trigger/before', $this->get_var( 'triggers' ), $this->get_var( 'selected' ), $this->get_var( 'post' ) ); ?>

	<?php $this->get_view( 'trigger/select' ); ?>

	<?php do_action( 'notification/metabox/trigger/after', $this->get_var( 'triggers' ), $this->get_var( 'selected' ), $this->get_var( 'post' ) ); ?>

<?php endif ?>
