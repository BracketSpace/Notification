<?php
/**
 * Merge tag metabox template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var callable(string $var_name, string $default=): void $the_esc Escaped variable printer.
 * @var BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

use BracketSpace\Notification\Core\Templates;

do_action( 'notification/metabox/trigger/tags/before', $get( 'trigger' ) );

$groups = $get( 'tag_groups' );

Templates::render( 'mergetag/searchbox' );

?>

<div class="notification_merge_tags_accordion">
	<?php do_action( 'notification/metabox/trigger/tags/groups/before', $get( 'trigger' ) ); ?>
	<?php foreach ( $groups as $group_key => $group_value ) : ?>
		<h2 data-group="<?php echo esc_html( sanitize_title( $group_key ) ); ?>"><?php echo esc_html( $group_key ); ?></h2>
		<?php if ( $group_value ) : ?>
			<ul class="tags-group" data-group="<?php echo esc_html( sanitize_title( $group_key ) ); ?>">
				<?php foreach ( $group_value as $tag ) : ?>
					<li>
						<?php Templates::render( 'mergetag/tag', [ 'tag' => $tag ] ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php do_action( 'notification/metabox/trigger/tags/groups/after', $get( 'trigger' ) ); ?>
</div>
<?php do_action( 'notification/metabox/trigger/tags/after', $get( 'trigger' ) ); ?>
