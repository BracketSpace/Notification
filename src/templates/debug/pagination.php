<?php
/**
 * Log pagination template
 *
 * @package notification
 *
 * @var callable(string $var_name, string $default=): mixed $get Variable getter.
 * @var callable(string $var_name, string $default=): void $the Variable printer.
 * @var BracketSpace\Notification\Vendor\Micropackage\Templates\Template $this Template instance.
 */

?>

<div class="log-pagination">
	<?php
	echo paginate_links( [ // phpcs:ignore
		'base'    => admin_url( 'edit.php?post_type=notification&page=settings&section=debugging&' . $get( 'query_arg' ) . '=%#%' ),
		'current' => $get( 'current' ),
		'total'   => $get( 'total' ),
	] );
	?>
</div>
