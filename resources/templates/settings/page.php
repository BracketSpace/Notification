<?php
/**
 * Settings page template
 *
 * @package notification
 *
 * @var BracketSpace\Notification\Utils\Settings $this Settings instance.
 */

if ( ! isset( $current_section ) ) {
	$current_section = '';
}

?>

<div class="wrap underdev-settings <?php echo esc_attr( $this->handle ); ?>-settings">

	<h1><?php esc_html_e( 'Settings', $this->textdomain ); ?></h1>

	<?php if ( empty( $sections ) ) : ?>
		<p><?php esc_html_e( 'No Settings available at the moment', $this->textdomain ); ?></p>
	<?php else : ?>

		<div class="menu-col">

			<?php do_action( $this->handle . '/settings/sidebar/before' ); ?>

			<ul class="menu-list box">
				<?php foreach ( $this->get_sections() as $section_slug => $section ) : ?>
					<?php
					$class    = ( $section_slug === $current_section ) ? 'current' : '';
					$page_url = remove_query_arg( 'updated' );
					$url      = add_query_arg( 'section', $section_slug, $page_url );
					?>
					<li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo esc_attr( $url ); ?>"><?php echo esc_html( $section->name() ); ?></a></li>
				<?php endforeach ?>
			</ul>

			<?php do_action( $this->handle . '/settings/sidebar/after' ); ?>

		</div>

		<?php $section = $this->get_section( $current_section ); ?>

		<div id="notification-settings-section-<?php echo esc_attr( $section->slug() ); ?>" class="setting-col box section-<?php echo esc_attr( $section->slug() ); ?>">

			<?php do_action( $this->handle . '/settings/section/' . $section->slug() . '/before' ); ?>

			<form action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">

				<?php wp_nonce_field( 'save_' . $this->handle . '_settings', 'nonce' ); ?>

				<input type="hidden" name="action" value="save_<?php echo esc_attr( $this->handle ); ?>_settings">

				<?php
				/**
				 * When you have only checkboxed in the section, no data in POST is returned. This fields adds a dummy value
				 * for form handler so it could grab the section name and parse all defined fields
				 */
				?>
				<input type="hidden" name="<?php echo esc_attr( $this->handle ) . '_settings[' . esc_attr( $section->slug() ) . ']'; ?>" value="section_buster">

				<?php $groups = $section->get_groups(); ?>

				<?php foreach ( $groups as $group ) : ?>

					<div id="notification-settings-group-<?php echo esc_attr( $group->slug() ); ?>" class="setting-group group-<?php echo esc_attr( $group->slug() ); ?>">
						<div class="setting-group-header <?php echo esc_attr( ( $group->collapsed() ) ? '' : 'open' ); ?>">
							<h3><?php echo esc_html( $group->name() ); ?></h3>

							<?php $description = $group->description(); ?>

							<?php if ( ! empty( $description ) ) : ?>
								<p class="description"><?php echo esc_html( $description ); ?></p>
							<?php endif ?>
						</div>

						<?php do_action( $this->handle . '/settings/group/' . $group->slug() . '/before' ); ?>
						<table class="form-table">

							<?php foreach ( $group->get_fields() as $field ) : ?>

								<tr id="notification-settings-field-<?php echo esc_attr( $group->slug() ); ?>-<?php echo esc_attr( $field->slug() ); ?>" class="field-<?php echo esc_attr( $field->slug() ); ?>">
									<th><label for="<?php echo esc_attr( $field->input_id() ); ?>"><?php echo esc_html( $field->name() ); ?></label></th>
									<td>
										<?php
										$field->render();
										$field_description = $field->description();
										?>
										<?php if ( ! empty( $field_description ) ) : ?>
											<small class="description"><?php echo wp_kses_data( $field_description ); ?></small>
										<?php endif ?>
									</td>
								</tr>

							<?php endforeach ?>

						</table>
						<?php do_action( $this->handle . '/settings/sections/after', $group->slug() ); ?>

					</div>

				<?php endforeach ?>

				<?php if ( ! empty( $groups ) ) : ?>
					<?php submit_button(); ?>
				<?php endif ?>

			</form>

			<?php do_action( $this->handle . '/settings/section/' . $section->slug() . '/after' ); ?>

		</div>

	<?php endif ?>

</div>
