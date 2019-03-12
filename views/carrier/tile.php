<div class="carrier-tile" id="<?php $this->echo_var( 'id' ); ?>" data-carrier-id="<?php $this->echo_var( 'id' ); ?>">
	<div class="inside">
		<?php do_action_deprecated( 'notification/notification/box/pre', [ $this ], '[Next]', 'notification/carrier/box/pre' ); ?>
		<?php do_action( 'notification/carrier/box/pre', $this ); ?>
		<div class="tile-image">
			<?php $this->echo_var( 'image' ); ?>
		</div>
		<h2 class="hndle"><span><?php $this->echo_var( 'title' ); ?></span></h2>
		<?php do_action_deprecated( 'notification/notification/box/post', [ $this ], '[Next]', 'notification/carrier/box/post' ); ?>
		<?php do_action( 'notification/carrier/box/post', $this ); ?>
	</div>
	<div class="carrier-tile-hover">
		<span class="dashicons dashicons-plus"></span>
		<?php _e( sprintf( 'Add %s', $this->get_var( 'title' ) ), 'notification' ); ?>
	</div>
</div>
