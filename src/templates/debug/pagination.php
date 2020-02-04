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
		'base'    => admin_url( 'edit.php?post_type=notification&page=settings&section=debugging&' . $this->get_var( 'query_arg' ) . '=%#%' ),
		'current' => $this->get_var( 'current' ),
		'total'   => $this->get_var( 'total' ),
	] );
	?>
</div>
