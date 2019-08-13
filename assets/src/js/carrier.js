(function($) {

	$( document ).ready( function() {

		$( '#carrier-boxes .postbox .switch-container input' ).change( function( event ) {
			$( this ).parents( '.switch' ).first().toggleClass( 'active' );
			wp.hooks.doAction( 'notification.carrier.toggled', $( this ) );
		} );

	} );

})(jQuery);
