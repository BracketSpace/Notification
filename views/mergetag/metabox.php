<?php
/**
 * Merge tag metabox template
 *
 * @package notification
 */

do_action( 'notification/metabox/trigger/tags/before', $this->get_var( 'trigger' ) );
?>

<ul>

	<?php if ( count( $this->get_var( 'tags') ) > 2 ): ?>
		<input type="text" name="notification-search-merge-tags" placeholder="<?php esc_attr_e( 'Search merge tags' ); ?>" class="widefat notification-search-merge-tags" autocomplete="off" id="notification-search-merge-tags">
	<?php endif; ?>
	<?php foreach ( $this->get_var( 'tags' ) as $tag ): ?>
		<li>
			<div class="intro">
				<label><?php echo $tag->get_name(); ?></label>
				<code data-clipboard-text="{<?php echo $tag->get_slug(); ?>}">{<?php echo $tag->get_slug(); ?>}</code>
			</div>
			<span class="question-mark">
				?
				<div class="description">
					<div class="description-container">
						<?php if ( $tag->is_description_example() ): ?>
							<label>
								<?php _e( 'Example:' ); ?>
							</label>
						<?php endif ?>
						<div class="description-content">
							<?php echo $tag->get_description(); ?>
						</div>
						<?php if ( $tag->is_description_example() ): ?>
							<i>(<?php echo $tag->get_value_type(); ?>)</i>
						<?php endif ?>
					</div>
				</div>
			</span>
		</li>
	<?php endforeach ?>
</ul>

<?php do_action( 'notification/metabox/trigger/tags/after', $this->get_var( 'trigger' ) ); ?>
