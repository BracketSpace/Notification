<?php
/**
 * Screen help tab template
 *
 * @package notification
 */

?>

<p><?php esc_html_e( 'You can use the below Merge Tags in any Trigger and any Carrier.', 'notification' ); ?></p>

<table>
	<?php foreach ( $this->get_var( 'tags' ) as $tag ) : ?>
		<tr>
			<td><strong><?php echo esc_attr( $tag->get_name() ); ?></strong></td>
			<td><code class="notification-merge-tag" data-clipboard-text="{<?php echo esc_attr( $tag->get_slug() ); ?>}">{<?php echo esc_html( $tag->get_slug() ); ?>}</code></td>
			<td>
				<?php $description = $tag->get_description(); ?>
				<?php if ( ! empty( $description ) ) : ?>
					<p class="description">
						<?php if ( $tag->is_description_example() ) : ?>
							<strong><?php esc_html_e( 'Example:', 'notification' ); ?></strong>
						<?php endif ?>
						<?php echo $description; // phpcs:ignore ?>
					</p>
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
</table>
