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
			<?php echo $tag->get_name(); ?> -
			<code data-clipboard-text="{<?php echo $tag->get_slug(); ?>}">{<?php echo $tag->get_slug(); ?>}</code>
			<p class="description"><?php echo $tag->get_description(); ?></p>
		</li>
	<?php endforeach ?>
</ul>

<?php do_action( 'notification/metabox/trigger/tags/after', $this->get_var( 'trigger' ) ); ?>
