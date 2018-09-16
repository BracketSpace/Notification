<?php
/**
 * Merge tag metabox template
 *
 * @package notification
 */

do_action( 'notification/metabox/trigger/tags/before', $this->get_var( 'trigger' ) );
?>

<ul>

	<?php if ( count( $this->get_var( 'tags' ) ) > 2 ) : ?>
		<input type="text" name="notification-search-merge-tags" placeholder="<?php esc_attr_e( 'Search merge tags', 'notification' ); ?>" class="widefat notification-search-merge-tags" autocomplete="off" id="notification-search-merge-tags">
	<?php endif; ?>
	<?php foreach ( $this->get_var( 'tags' ) as $tag ) : ?>
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
								<?php echo $description; // WPCS: XSS ok. ?>
							</div>
							<?php if ( $tag->is_description_example() ) : ?>
								<i>(<?php echo esc_html( $tag->get_value_type() ); ?>)</i>
							<?php endif ?>
						</div>
					</div>
				</span>
			<?php endif ?>
		</li>
	<?php endforeach ?>
</ul>

<?php do_action( 'notification/metabox/trigger/tags/after', $this->get_var( 'trigger' ) ); ?>
