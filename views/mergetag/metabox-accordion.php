<?php
/**
 * Merge tag metabox template
 *
 * @package notification
 */

do_action( 'notification/metabox/trigger/tags/before', $this->get_var( 'trigger' ) );

$groups = $this->get_var( 'tag_groups' );

$this->get_view( 'mergetag/searchbox' );

?>

<div class="notification_merge_tags_accordion">
	<?php foreach ( $groups as $group_key => $group_value ) : ?>
		<h2 data-group="<?php echo esc_html( sanitize_title( $group_key ) ); ?>"><?php echo esc_html( $group_key ); ?></h2>
		<?php if ( $group_value ) : ?>
			<?php do_action( 'notification/metabox/trigger/tags/groups/before', $this->get_var( 'trigger' ) ); ?>
			<ul class="tags-group" data-group="<?php echo esc_html( sanitize_title( $group_key ) ); ?>">
				<?php do_action( 'notification/metabox/trigger/tags/groups/before_first', $this->get_var( 'trigger' ) ); ?>
				<?php foreach ( $group_value as $tag ) : ?>
					<li>
						<?php
						$this->set_var( 'tag', $tag, true );
						$this->get_view( 'mergetag/tag' );
						?>
					</li>
				<?php endforeach; ?>
				<?php do_action( 'notification/metabox/trigger/tags/groups/after_last', $this->get_var( 'trigger' ) ); ?>
			</ul>
			<?php do_action( 'notification/metabox/trigger/tags/groups/after', $this->get_var( 'trigger' ) ); ?>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
<?php do_action( 'notification/metabox/trigger/tags/after', $this->get_var( 'trigger' ) ); ?>
