(function($) {

	$( document ).ready( function() {

		$( '#notification-boxes .postbox .switch-container input' ).change( function( event ) {
			$( this ).parents( '.switch' ).first().toggleClass( 'active' );
			notification.hooks.doAction( 'notification.notification.toggled', $( this ) );
		} );

	} );

})(jQuery);
