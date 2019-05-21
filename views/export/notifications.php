<?php
/**
 * Export notifications form
 *
 * @package notification
 */

$notifications = $this->get_var( 'notifications' );

?>

<?php if ( empty( $notifications ) ) : ?>
	<p><?php esc_html_e( 'You don\'t have any notifications yet' ); ?></p>
<?php else : ?>

	<div id="export-notifications">
		<ul>
			<li><label><input type="checkbox" name="export-items" class="select-all"> <strong><?php esc_html_e( 'Select all' ); ?></strong></label></li>
			<?php foreach ( $notifications as $notification ) : ?>
				<li><label><input type="checkbox" name="export-items" value="<?php echo esc_attr( $notification->get_id() ); ?>"> <?php echo esc_html( $notification->get_title() ); ?></label></li>
			<?php endforeach ?>
		</ul>
		<a href="<?php $this->echo_var( 'download_link' ); ?>" class="button button-secondary"><?php esc_html_e( 'Download JSON' ); ?></a>
	</div>

<?php endif ?>
