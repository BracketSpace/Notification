<?php
/**
 * Widget to showing / removing carriers
 *
 * @package notification
 */

?>
<div class="notificationCarriers" data-nt-widget <?php echo ( $this->get_var( 'carriers_added_count' ) === $this->get_var( 'carriers_exists_count' ) ) ? 'data-nt-hidden' : ''; ?>>
	<ul class="notificationCarriers__carriers" data-nt-buttons data-nt-hidden>
		<?php foreach ( $this->get_var( 'carriers' ) as $carrier ) : ?>
			<li class="notificationCarriers__carrier"
				data-nt-button="<?php echo esc_attr( $carrier->get_slug() ); ?>"
				<?php echo ( $carrier->is_actived( $this->get_var( 'carriers_exists' ) ) ) ? 'data-nt-hidden' : ''; ?>>
				<a href="#" class="notificationCarriers__carrierLink" data-nt-button-link>
					<div class="notificationCarriers__carrierMedia">
						<div class="notificationCarriers__carrierIcon"><?php echo $carrier->icon; // phpcs:ignore ?></div>
					</div>
					<div class="notificationCarriers__carrierTitle"><?php echo esc_html( $carrier->get_name() ); ?></div>
					<div class="notificationCarriers__carrierOverlay">
						<div class="notificationCarriers__carrierOverlayInner">
							<div class="notificationCarriers__carrierOverlayIcon"></div>
							<div class="notificationCarriers__carrierOverlayTitle"><?php echo esc_html__( 'Add Carrier', 'notification' ); ?></div>
						</div>
					</div>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="notificationCarriers__button">
		<a href="#" class="notificationCarriers__buttonLink" data-nt-widget-add>
			<div class="notificationCarriers__buttonLinkInner">
				<div class="notificationCarriers__buttonIcon notificationCarriers__buttonIcon--add"></div>
				<div class="notificationCarriers__buttonTitle"><?php echo esc_html__( 'Add New Carrier', 'notification' ); ?></div>
			</div>
		</a>
	</div>

	<div class="notificationCarriers__button">
		<a href="#" class="notificationCarriers__buttonLink notificationCarriers__buttonLink--less" data-nt-widget-abort data-nt-hidden>
			<div class="notificationCarriers__buttonLinkInner">
				<div class="notificationCarriers__buttonIcon notificationCarriers__buttonIcon--close"></div>
				<div class="notificationCarriers__buttonTitle"><?php echo esc_html__( 'Abort', 'notification' ); ?></div>
			</div>
		</a>
	</div>
</div>
