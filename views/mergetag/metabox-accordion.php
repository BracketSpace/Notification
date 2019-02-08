<?php
/**
 * Merge tag metabox template
 *
 * @package notification
 */

do_action( 'notification/metabox/trigger/tags/before', $this->get_var( 'trigger' ) );

$groups = $this->get_var( 'tag_groups' );

?>
<input type="text" name="notification-search-merge-tags" placeholder="<?php esc_attr_e( 'Search merge tags', 'notification' ); ?>" class="widefat notification-search-merge-tags" autocomplete="off" id="notification-search-merge-tags">
	<div class="notification_merge_tags_accordion">
		<?php foreach ( $groups as $group_key => $group_value ) : ?>
			<h2 data-group="<?php echo esc_html( sanitize_title( $group_key ) ); ?>"><?php echo esc_html( $group_key ); ?></h2>
			<?php if ( $group_value ) : ?>
				<ul class="tags-group" data-group="<?php echo esc_html( sanitize_title( $group_key ) ); ?>">
					<?php foreach ( $group_value as $tag ) : ?>
						<li>
							<div class="intro">
								<label><?php echo esc_html( $tag->get_name() ); ?></label>
								<code class="notification-merge-tag" data-clipboard-text="{<?php echo esc_attr( $tag->get_slug() ); ?>}">{<?php echo esc_attr( $tag->get_slug() ); ?>}</code>
							</div>
							<?php $description = $tag->get_description(); ?>
							<?php if ( ! empty( $description ) ) : ?>
								<span class="question-mark">
									?
									<div class="description">
										<div class="description-container">
											<?php if ( $tag->is_description_example() ) : ?>
												<label><?php esc_html_e( 'Example:', 'notification' ); ?></label>
											<?php endif ?>
											<div class="description-content">
												<?php echo $description; // phpcs:ignore ?>
											</div>
											<?php if ( $tag->is_description_example() ) : ?>
												<i>(<?php echo esc_html( $tag->get_value_type() ); ?>)</i>
											<?php endif ?>
										</div>
									</div>
								</span>
							<?php endif ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php do_action( 'notification/metabox/trigger/tags/after', $this->get_var( 'trigger' ) ); ?>
