<?php
/**
 * Merge tag metabox template
 *
 * @package notification
 */

do_action( 'notification/metabox/trigger/tags/before', $this->get_var( 'trigger' ) );
?>

<ul>
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
						<label>
						<?php _e( 'Example:' ); ?>
						</label>
						<div class="description-content">
							<?php echo $tag->get_description(); ?>
						</div>
					</div>
				</div>
			</span>
		</li>
	<?php endforeach ?>
</ul>

<?php do_action( 'notification/metabox/trigger/tags/after', $this->get_var( 'trigger' ) ); ?>
