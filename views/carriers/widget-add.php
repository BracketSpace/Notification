<?php
/**
 * Widget to showing / removing carriers
 *
 * @package notification
 */

?>
<div class="notification-carriers" data-nt-widget <?php echo ( $this->get_var( 'carriers_added_count' ) === $this->get_var( 'carriers_exists_count' ) ) ? 'data-nt-hidden' : ''; ?>>
	<ul class="notification-carriers__carriers" data-nt-buttons data-nt-hidden>
		<?php foreach ( $this->get_var( 'carriers' ) as $carrier ) : ?>
			<li class="notification-carriers__carrier"
				data-nt-button="<?php echo esc_attr( $carrier->get_slug() ); ?>"
				<?php echo ( array_key_exists( $carrier->get_slug(), $this->get_var( 'carriers_exists' ) ) ) ? 'data-nt-hidden' : ''; ?>>
				<a href="#" class="notification-carriers__carrier-link" data-nt-button-link>
					<div class="notification-carriers__carrier-media">
						<div class="notification-carriers__carrier-icon"><?php echo $carrier->icon; // phpcs:ignore ?></div>
					</div>
					<div class="notification-carriers__carrier-title"><?php echo esc_html( $carrier->get_name() ); ?></div>
					<div class="notification-carriers__carrier-overlay">
						<div class="notification-carriers__carrier-overlay-inner">
							<div class="notification-carriers__carrier-overlay-icon"></div>
							<div class="notification-carriers__carrier-overlay-title"><?php echo esc_html__( 'Add Carrier', 'notification' ); ?></div>
						</div>
					</div>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="notification-carriers__button">
		<a href="#" class="notification-carriers__button-link" data-nt-widget-add>
			<div class="notification-carriers__button-link-inner">
				<div class="notification-carriers__button-icon notification-carriers__button-icon--add"></div>
				<div class="notification-carriers__button-title"><?php echo esc_html__( 'Add New Carrier', 'notification' ); ?></div>
			</div>
		</a>
	</div>

	<div class="notification-carriers__button">
		<a href="#" class="notification-carriers__button-link notification-carriers__button-link--less" data-nt-widget-abort data-nt-hidden>
			<div class="notification-carriers__button-link-inner">
				<div class="notification-carriers__button-icon notification-carriers__button-icon--close"></div>
				<div class="notification-carriers__button-title"><?php echo esc_html__( 'Abort', 'notification' ); ?></div>
			</div>
		</a>
	</div>
</div>
