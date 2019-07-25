<?php
/**
 * Box template
 *
 * @package notification
 */

?>

<div id="<?php $this->echo_var( 'id' ); ?>" class="postbox carrier-panel <?php echo $this->get_var( 'shown' ) ? 'shown' : ''; ?>">
	<div class="delete-container">
			<button class="delete-carrier">
				<span class="dashicons dashicons-trash"></span>
			</button>
			<input class="active" type="hidden" name="<?php $this->echo_var( 'name' ); ?>" value="<?php echo $this->get_var( 'shown' ) ? 1 : 0; ?>"/>
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
