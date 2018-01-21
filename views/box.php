<div id="<?php $this->echo_var( 'id' ); ?>" class="postbox <?php if ( ! $this->get_var( 'open' ) ) echo 'closed'; ?>">
	<div class="switch-container">
		<label class="switch <?php if ( $this->get_var( 'open' ) ) echo 'active'; ?>">
			<input type="checkbox" name="<?php $this->echo_var( 'name' ); ?>" value="1" <?php checked( $this->get_var( 'open' ), true ); ?> />
			<div></div>
		</label>
	</div>
	<h2 class="hndle"><span><?php $this->echo_var( 'title' ); ?></span></h2>
	<div class="inside">
		<?php $this->echo_var( 'content' ); ?>
	</div>
</div>
