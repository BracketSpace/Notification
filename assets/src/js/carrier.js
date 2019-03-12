(function($) {

	$( document ).ready( function() {

		$( '#carrier-boxes .postbox .switch-container input' ).change( function( event ) {
			$( this ).parents( '.switch' ).first().toggleClass( 'active' );
			notification.hooks.doAction( 'notification.carrier.toggled', $( this ) );
		} );

		$( '#notification_carrier_add' ).on( 'click', function(e) {
			e.preventDefault();
			$(this).fadeOut();
			$( '#notification_carrier_wizard' ).css("display", "flex").hide().fadeIn();
			$( '#notification_carrier_abort' ).fadeIn();
		} );

		$( '#notification_carrier_abort' ).on( 'click', function(e) {
			e.preventDefault();
			$(this).fadeOut();
			$( '#notification_carrier_wizard' ).fadeOut();
			$( '#notification_carrier_add' ).fadeIn();
		} );

	} );

})(jQuery);
