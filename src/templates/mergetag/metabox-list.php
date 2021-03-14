<?php
/**
 * Merge tag metabox template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var BracketSpace\Notification\Vendor\Micropackage\Templates\Template $this Template instance.
 */

do_action( 'notification/metabox/trigger/tags/before', $get( 'trigger' ) );

$tags = $get( 'tags' );

notification_template( 'mergetag/searchbox' );

?>

<ul>
	<?php do_action( 'notification/metabox/trigger/tags/list/before', $get( 'trigger' ) ); ?>
	<?php foreach ( $tags as $tag ) : ?>
		<li>
			<?php notification_template( 'mergetag/tag', [ 'tag' => $tag ] ); ?>
		</li>
	<?php endforeach ?>
	<?php do_action( 'notification/metabox/trigger/tags/list/after', $get( 'trigger' ) ); ?>
</ul>

<?php do_action( 'notification/metabox/trigger/tags/after', $get( 'trigger' ) ); ?>
