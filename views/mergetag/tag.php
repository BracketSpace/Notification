<?php
/**
 * Merge tag template
 *
 * @package notification
 */

$tag = $this->get_var( 'tag' );

?>

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
