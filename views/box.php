<?php
/**
 * Notification box template
 *
 * @package notification
 */

?>

<div id="<?php $this->echo_var( 'id' ); ?>" class="postbox <?php echo ! $this->get_var( 'open' ) ? 'closed' : ''; ?>">
	<div class="switch-container">
		<label class="switch <?php echo $this->get_var( 'open' ) ? 'active' : ''; ?>">
			<input type="checkbox" name="<?php $this->echo_var( 'name' ); ?>" value="1" <?php checked( $this->get_var( 'open' ), true ); ?> />
			<div></div>
		</label>
	</div>
	<h2 class="hndle"><span><?php $this->echo_var( 'title' ); ?></span></h2>
	<div class="inside">
		<?php do_action( 'notification/notification/box/pre', $this ); ?>
		<?php $this->echo_var( 'content' ); ?>
		<?php do_action( 'notification/notification/box/post', $this ); ?>
	</div>
</div>
