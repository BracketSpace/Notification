(function($) {

	$( document ).ready( function() {

		var wizard = $( '#notification_carrier_wizard' ),
				addButton = $( '#notification_carrier_add' ),
				deleteButton = $( '.delete-carrier' ),
				abortButton = $( '#notification_carrier_abort' ),
				carrierBoxesCount = $('#carrier-boxes').data('carriers-count');
				carriersEnabledCount = $('#carrier-boxes').data('enabled-carriers-count');

		addButton.on( 'click', function( e ) {
			e.preventDefault();
			$( this ).fadeOut(500);
			setTimeout(function(){
				wizard.css("display", "flex").hide().fadeIn(500);
			}, 600);
		} );

		deleteButton.on('click', function(e) {
			e.preventDefault();
			$(this).parent().find( '.active' ).val(0);
			$(this).parents( '.carrier-panel' ).removeClass('shown');
			addButton.fadeIn();
		} );

		abortButton.on( 'click', function(e) {
			e.preventDefault();
			wizard.fadeOut(500);
			setTimeout(function(){
				addButton.fadeIn(500);
			}, 600);
		} );

		$( '.carrier-tile' ).on( 'click', function(e) {
			e.preventDefault()
			var data = $(this).data( 'carrier-id' );
			wizard.fadeOut(500);
			var carrier = $( ".carrier-panel[id=" + data + "]" );
			carrier.addClass( 'shown' );
			carrier.find( '.active' ).val(1);
			carriersEnabledCount++;
			setTimeout(function(){
				if( carriersEnabledCount < carrierBoxesCount ) {
					addButton.fadeIn(500);
				}
			}, 600);
		});
	} );
})(jQuery);
