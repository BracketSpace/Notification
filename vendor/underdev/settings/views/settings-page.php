<div class="wrap underdev-settings <?php echo $this->handle; ?>-settings">

	<h1><?php _e( 'Settings', $this->textdomain ) ?></h1>

	<?php if ( empty( $sections ) ): ?>
		<p><?php _e( 'No Settings available at the moment', $this->textdomain ); ?></p>
	<?php else: ?>

		<div class="menu-col box">

			<ul class="menu-list">

				<?php foreach ( $this->get_sections() as $section_slug => $section ): ?>

					<?php
					$class = ( $section_slug == $current_section ) ? 'current' : '';
					$page_url = remove_query_arg( 'updated' );
					$url = add_query_arg( 'section', $section_slug, $page_url );
					?>

					<li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo esc_attr( $url ); ?>"><?php echo esc_html( $section->name() ) ?></a></li>

				<?php endforeach ?>

			</ul>

		</div>

		<div class="setting-col box">

			<?php $section = $this->get_section( $current_section ); ?>

			<?php do_action( $this->handle . '/settings/section/' . $section->slug() . '/before' ); ?>

			<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" enctype="multipart/form-data">

				<?php wp_nonce_field( 'save_' . $this->handle . '_settings', 'nonce' ); ?>

				<input type="hidden" name="action" value="save_<?php echo $this->handle; ?>_settings">

				<?php
				/**
				 * When you have only checkboxed in the section, no data in POST is returned. This fields adds a dummy value
				 * for form handler so it could grab the section name and parse all defined fields
				 */
				?>
				<input type="hidden" name="<?php echo $this->handle . '_settings[' . $section->slug() . ']' ?>" value="section_buster">

				<?php $groups  = $section->get_groups(); ?>

				<?php foreach ( $groups as $group ): ?>

					<div class="setting-group">

						<h3><?php echo esc_html( $group->name() ); ?></h3>

						<?php $description = $group->description(); ?>

						<?php if ( ! empty( $description ) ): ?>
							<p class="description"><?php echo esc_html( $description ); ?></p>
						<?php endif ?>

						<?php do_action( $this->handle . '/settings/group/' . $group->slug() . '/before' ); ?>

						<table class="form-table">

							<?php foreach ( $group->get_fields() as $field ): ?>

								<tr>
									<th><label for="<?php echo esc_attr( $field->input_id() ); ?>"><?php echo esc_html( $field->name() ); ?></label></th>
									<td>
										<?php
										$field->render();
										$field_description = $field->description();
										?>
										<?php if ( ! empty( $field_description ) ): ?>
											<p><?php echo esc_html( $field_description ); ?></p>
										<?php endif ?>
									</td>
								</tr>

							<?php endforeach ?>

						</table>

						<?php do_action( $this->handle . '/settings/sections/after', $group->slug() ); ?>

					</div>

				<?php endforeach ?>

				<?php if ( ! empty( $groups ) ): ?>
					<?php submit_button(); ?>
				<?php endif ?>

			</form>

			<?php do_action( $this->handle . '/settings/section/' . $section->slug() . '/after' ); ?>

		</div>

	<?php endif ?>

</div>
