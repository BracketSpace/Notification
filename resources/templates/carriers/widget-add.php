<?php
/**
 * Widget to showing / removing carriers
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var callable(string $var_name, string $default=): void $the_esc Escaped variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

use BracketSpace\Notification\Dependencies\enshrined\svgSanitize\Sanitizer;

$svg_sanitizer = new Sanitizer();

?>
<div class="notification-carriers" data-nt-widget <?php echo ( $get( 'carriers_added_count' ) === $get( 'carriers_exists_count' ) ) ? 'data-nt-hidden' : ''; ?>>
	<ul class="notification-carriers__carriers" data-nt-buttons data-nt-hidden>
		<?php do_action( 'notification/carrier/list/before' ); ?>
		<?php foreach ( $get( 'carriers' ) as $carrier ) : ?>
			<li class="notification-carriers__carrier"
				data-nt-button="<?php echo esc_attr( $carrier->get_slug() ); ?>"
				<?php echo ( array_key_exists( $carrier->get_slug(), $get( 'carriers_exists' ) ) ) ? 'data-nt-hidden' : ''; ?>>
				<a href="#" class="notification-carriers__carrier-link" data-nt-button-link>
					<div class="notification-carriers__carrier-media">
						<div class="notification-carriers__carrier-icon">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $svg_sanitizer->sanitize( $carrier->icon );
							?>
						</div>
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
		<?php do_action( 'notification/carrier/list/after' ); ?>
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
