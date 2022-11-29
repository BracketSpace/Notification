<?php

declare(strict_types=1);

/**
 * Box template
 *
 * @package notification
 *
 * @var callable(string $varName, string $default=): mixed $get Variable getter.
 * @var callable(string $varName, string $default=): void $the Variable printer.
 * @var callable(string $varName, string $default=): void $theEsc Escaped variable printer.
 * @var \BracketSpace\Notification\Dependencies\Micropackage\Templates\Template $this Template instance.
 */

?>

<div id="<?php $theEsc('id'); ?>" class="postbox <?php echo ( ! $get('open') && $get('active') ) ? 'closed' : ''; ?>" data-nt-carrier <?php echo ( ! $get('active') ) ? 'data-nt-hidden' : ''; ?>>
	<div class="switch-container">
		<input id="carrier-toggle-<?php $theEsc('id'); ?>" type="checkbox" name="<?php $theEsc('name'); ?>" value="1" <?php checked(( $get('open') || ! $get('active') ), true); ?> data-nt-carrier-input-switch />
		<label for="carrier-toggle-<?php $theEsc('id'); ?>" class="switch">
			<div></div>
		</label>
		<button type="button" data-nt-carrier-remove></button>
	</div>
	<h2 class="hndle"><span><?php $theEsc('title'); ?></span></h2>
	<div class="inside">
		<?php do_action_deprecated('notification/notification/box/pre', [ $this ], '6.0.0', 'notification/carrier/box/pre'); ?>
		<?php do_action('notification/carrier/box/pre', $this); ?>
		<?php $the('content'); ?>
		<?php do_action_deprecated('notification/notification/box/post', [ $this ], '6.0.0', 'notification/carrier/box/post'); ?>
		<?php do_action('notification/carrier/box/post', $this); ?>
	</div>
</div>
