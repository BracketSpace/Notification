<?php
/**
 * Box template
 *
 * @package notification
 */

?>

<div id="<?php $the( 'id' ); ?>" class="postbox <?php echo ( ! $get( 'open' ) && $get( 'active' ) ) ? 'closed' : ''; ?>" data-nt-carrier <?php echo ( ! $get( 'active' ) ) ? 'data-nt-hidden' : ''; ?>>
	<div class="switch-container">
		<input id="carrier-toggle-<?php $the( 'id' ); ?>" type="checkbox" name="<?php $the( 'name' ); ?>" value="1" <?php checked( ( $get( 'open' ) || ! $get( 'active' ) ), true ); ?> data-nt-carrier-input-switch />
		<label for="carrier-toggle-<?php $the( 'id' ); ?>" class="switch">
			<div></div>
		</label>
		<button type="button" data-nt-carrier-remove></button>
	</div>
	<h2 class="hndle"><span><?php $the( 'title' ); ?></span></h2>
	<div class="inside">
		<?php do_action_deprecated( 'notification/notification/box/pre', [ $this ], '6.0.0', 'notification/carrier/box/pre' ); ?>
		<?php do_action( 'notification/carrier/box/pre', $this ); ?>
		<?php $the( 'content' ); ?>
		<?php do_action_deprecated( 'notification/notification/box/post', [ $this ], '6.0.0', 'notification/carrier/box/post' ); ?>
		<?php do_action( 'notification/carrier/box/post', $this ); ?>
	</div>
</div>
