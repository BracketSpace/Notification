<?php
/**
 * Box template
 *
 * @package notification
 */

?>

<div id="<?php $this->echo_var( 'id' ); ?>" class="postbox carrier-panel <?php echo $this->get_var( 'shown' ) ? 'shown' : ''; ?>">
	<div class="switch-container">
			<button class="delete-carrier">
				<?php _e( 'Delete Carrier', 'notification' ); ?>
			</button>
			<input type="hidden" name="<?php $this->echo_var( 'name' ); ?>" value="<?php echo $this->get_var( 'shown' ) ? 1 : 0; ?>"/>
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
