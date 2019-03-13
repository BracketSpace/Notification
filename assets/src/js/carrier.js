(function($) {

	$( document ).ready( function() {

		$( '#carrier-boxes .postbox .switch-container input' ).change( function( event ) {
			$( this ).parents( '.switch' ).first().toggleClass( 'active' );
			notification.hooks.doAction( 'notification.carrier.toggled', $( this ) );
		} );

		$( '#notification_carrier_add' ).on( 'click', function(e) {
			e.preventDefault();
			$(this).fadeOut(200);
			setTimeout( function() {
				$( '#notification_carrier_wizard' ).css("display", "flex").hide().fadeIn(400);
				$( '#notification_carrier_abort' ).fadeIn(400);
			}, 400);
		} );

		$( '#notification_carrier_abort' ).on( 'click', function(e) {
			e.preventDefault();
			$(this).fadeOut(200);
			$( '#notification_carrier_wizard' ).fadeOut(400);
			setTimeout(function(){
				$( '#notification_carrier_add' ).fadeIn(400);
			}, 400);
		} );

		$( '.carrier-tile' ).on('click', function(e) {
			e.preventDefault()
			var data = $(this).data('carrier-id');
			$('#notification_carrier_wizard').fadeOut(200);
			$('#notification_carrier_abort').fadeOut(200);
			setTimeout( function() {
				$( ".carrier-panel[id=" + data + "]").addClass('shown');
				$('#notification_carrier_add').fadeIn().addClass('open');
			}, 200 );
		});

		$('.carrier-panel-delete').on('click', function(e) {
			e.preventDefault();
			$(this).removeClass('shown');
		});

	} );

})(jQuery);
