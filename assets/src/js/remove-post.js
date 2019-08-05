( function( $ ) {

	var __ = wp.i18n.__;

	$(document).ready( function() {

		$('.notification-delete-post').click( function( e ) {
			if ( ! confirm( __( 'Are you sure you want to permanently delete the post?', 'notification' ) ) ) {
				e.preventDefault();
			}
		});

	} );

} )( jQuery );
