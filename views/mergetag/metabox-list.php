<?php
/**
 * Merge tag metabox template
 *
 * @package notification
 */

do_action( 'notification/metabox/trigger/tags/before', $this->get_var( 'trigger' ) );

$tags = $this->get_var( 'tags' );

$this->get_view( 'mergetag/searchbox' );

?>

<ul>
	<?php foreach ( $tags as $tag ) : ?>
		<li>
			<?php
			$this->set_var( 'tag', $tag, true );
			$this->get_view( 'mergetag/tag' );
			?>
		</li>
	<?php endforeach ?>
</ul>
<?php do_action( 'notification/metabox/trigger/tags/after', $this->get_var( 'trigger' ) ); ?>
