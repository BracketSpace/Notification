<?php
/**
 * Log pagination template
 *
 * @package notification
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
