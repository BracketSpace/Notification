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
	<?php do_action( 'notification/metabox/trigger/tags/groups/before', $this->get_var( 'trigger' ) ); ?>
	<?php foreach ( $groups as $group_key => $group_value ) : ?>
		<h2 data-group="<?php echo esc_html( sanitize_title( $group_key ) ); ?>"><?php echo esc_html( $group_key ); ?></h2>
		<?php if ( $group_value ) : ?>
			<ul class="tags-group" data-group="<?php echo esc_html( sanitize_title( $group_key ) ); ?>">
				<?php foreach ( $group_value as $tag ) : ?>
					<li>
						<?php
						$this->set_var( 'tag', $tag, true );
						$this->get_view( 'mergetag/tag' );
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php do_action( 'notification/metabox/trigger/tags/groups/after', $this->get_var( 'trigger' ) ); ?>
</div>
<?php do_action( 'notification/metabox/trigger/tags/after', $this->get_var( 'trigger' ) ); ?>
