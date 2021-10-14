<?php
/**
 * Custom Fields Merge Tags metabox content
 *
 * @package notification
 */

$extension_link = sprintf(
	'<a href="https://bracketspace.com/downloads/notification-custom-fields/?utm_source=wp&utm_medium=notification-edit&utm_id=upsell" target="_blank">%s</a>',
	__( 'Custom Fields extension', 'notification' )
);

?>

<h2 data-group="custom-fields"><?php esc_html_e( 'Custom Fields', 'notification' ); ?></h2>
<ul class="tags-group" data-group="custom-fields">
	<span class="label-pro">PRO</span>
	<?php
	// phpcs:disable
	printf(
		// Translators: Link to extension.
		__( 'Install %s to use merge tags like: %s', 'notification' ),
		$extension_link,
		'<code>{postmeta …}</code>, <code>{usermeta …}</code>, <code>{commentmeta …}</code>, or <code>{acf …}</code>'
	);
	// phpcs:enable
	?>
</ul>
