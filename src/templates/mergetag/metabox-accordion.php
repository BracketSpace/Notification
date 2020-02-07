<?php
/**
 * Merge tag metabox template
 *
 * @package notification
 */

do_action( 'notification/metabox/trigger/tags/before', $get( 'trigger' ) );

$groups = $get( 'tag_groups' );

notification_template( 'mergetag/searchbox' );

?>

<div class="notification_merge_tags_accordion">
	<?php do_action( 'notification/metabox/trigger/tags/groups/before', $get( 'trigger' ) ); ?>
	<?php foreach ( $groups as $group_key => $group_value ) : ?>
		<h2 data-group="<?php echo esc_html( sanitize_title( $group_key ) ); ?>"><?php echo esc_html( $group_key ); ?></h2>
		<?php if ( $group_value ) : ?>
			<ul class="tags-group" data-group="<?php echo esc_html( sanitize_title( $group_key ) ); ?>">
				<?php foreach ( $group_value as $tag ) : ?>
					<li>
						<?php notification_template( 'mergetag/tag', [ 'tag' => $tag ] ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php do_action( 'notification/metabox/trigger/tags/groups/after', $get( 'trigger' ) ); ?>
</div>
<?php do_action( 'notification/metabox/trigger/tags/after', $get( 'trigger' ) ); ?>
