<?php
/**
 * Box template
 *
 * @package notification
 */

?>

<div id="<?php $this->echo_var( 'id' ); ?>" class="postbox <?php echo ( ! $this->get_var( 'open' ) && $this->get_var( 'active' ) ) ? 'closed' : ''; ?>" data-nt-carrier <?php echo ( ! $this->get_var( 'active' ) ) ? 'data-nt-hidden' : ''; ?>>
	<div class="switch-container">
		<input id="carrier-toggle-<?php $this->echo_var( 'id' ); ?>" type="checkbox" name="<?php $this->echo_var( 'name' ); ?>" value="1" <?php checked( ( $this->get_var( 'open' ) || ! $this->get_var( 'active' ) ), true ); ?> data-nt-carrier-input-switch />
		<label for="carrier-toggle-<?php $this->echo_var( 'id' ); ?>" class="switch">
			<div></div>
		</label>
		<button type="button" data-nt-carrier-remove></button>
	</div>
	<h2 class="hndle"><span><?php $this->echo_var( 'title' ); ?></span></h2>
	<div class="inside">
		<?php do_action_deprecated( 'notification/notification/box/pre', [ $this ], '6.0.0', 'notification/carrier/box/pre' ); ?>
		<?php do_action( 'notification/carrier/box/pre', $this ); ?>
		<?php $this->echo_var( 'content' ); ?>
		<?php do_action_deprecated( 'notification/notification/box/post', [ $this ], '6.0.0', 'notification/carrier/box/post' ); ?>
		<?php do_action( 'notification/carrier/box/post', $this ); ?>
	</div>
</div>
