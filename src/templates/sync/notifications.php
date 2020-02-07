<?php
/**
 * Notifications template
 *
 * @package notification
 */

?>

<table class="message-setting-table">
	<thead>
		<tr>
			<td><?php esc_html_e( 'Title' ); ?></td>
			<td><?php esc_html_e( 'Hash' ); ?></td>
			<td><?php esc_html_e( 'Status' ); ?></td>
			<td><?php esc_html_e( 'Action' ); ?></td>
		</tr>
	</thead>
	<?php foreach ( $get( 'collection' ) as $item ) : ?>
		<?php $hash = $item['notification']->get_hash(); ?>
		<tr>
			<td class="title">
				<?php if ( isset( $item['post_id'] ) ) : ?>
					<a href="<?php echo esc_url( get_edit_post_link( $item['post_id'], 'admin' ) ); ?>">
				<?php endif ?>
				<?php echo esc_html( $item['notification']->get_title() ); ?>
				<?php if ( isset( $item['post_id'] ) ) : ?>
					</a>
				<?php endif ?>
			</td>
			<td class="hash">
				<code><?php echo esc_html( $hash ); ?></code>
			</td>
			<td class="status">
				<?php if ( ! $item['up_to_date'] ) : ?>
					<?php if ( 'WordPress' === $item['source'] ) : ?>
						<?php if ( ! $item['has_json'] ) : ?>
							<?php esc_html_e( 'WordPress only' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'JSON outdated' ); ?>
						<?php endif ?>
					<?php elseif ( 'JSON' === $item['source'] ) : ?>
						<?php esc_html_e( 'JSON only' ); ?>
					<?php endif ?>
				<?php else : ?>
					<?php esc_html_e( 'Synchronized' ); ?>
				<?php endif ?>
			</td>
			<td class="action">
				<?php if ( ! $item['up_to_date'] ) : ?>
					<?php if ( 'WordPress' === $item['source'] ) : ?>
						<a href="#" class="button button-secondary notification-sync button-small" data-sync-hash="<?php echo esc_attr( $hash ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'notification_sync_' . $hash ) ); ?>" data-sync-type="json"><?php esc_html_e( 'Save to JSON' ); ?></a>
					<?php elseif ( 'JSON' === $item['source'] ) : ?>
						<a href="#" class="button button-secondary notification-sync button-small" data-sync-hash="<?php echo esc_attr( $hash ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'notification_sync_' . $hash ) ); ?>" data-sync-type="wordpress"><?php esc_html_e( 'Load to WordPress' ); ?></a>
					<?php endif ?>
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
</table>
