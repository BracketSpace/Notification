<?php
/**
 * Box template
 *
 * @package notification
 */

?>

<div id="<?php $this->echo_var( 'id' ); ?>" class="postbox carrier-panel <?php echo ! $this->get_var( 'open' ) ? 'closed' : ''; ?>">
	<div class="switch-container">
		<label class="switch <?php echo $this->get_var( 'open' ) ? 'active' : ''; ?>">
			<input type="checkbox" name="<?php $this->echo_var( 'name' ); ?>" value="1" <?php checked( $this->get_var( 'open' ), true ); ?> />
			<div></div>
		</label>
	</div>
	<h2 class="hndle"><span><?php $this->echo_var( 'title' ); ?></span></h2>
	<div class="inside">
		<?php do_action_deprecated( 'notification/notification/box/pre', [ $this ], '[Next]', 'notification/carrier/box/pre' ); ?>
		<?php do_action( 'notification/carrier/box/pre', $this ); ?>
		<?php $this->echo_var( 'content' ); ?>
		<?php do_action_deprecated( 'notification/notification/box/post', [ $this ], '[Next]', 'notification/carrier/box/post' ); ?>
		<?php do_action( 'notification/carrier/box/post', $this ); ?>
	</div>
</div>
