( function($) {

	$( document ).ready( function() {

		$( '.notification-pretty-select:visible' ).selectize();

		wp.hooks.addAction( 'notification.carrier.recipients.recipient.replaced', 'notification', function( $input ) {
			if ( $input.hasClass( 'notification-pretty-select' ) ) {
				$input.selectize();
			}
		} );

		wp.hooks.addAction( 'notification.repeater.row.added', 'notification', function( $row, $repeater ) {
			$row.find( 'select.notification-pretty-select' ).each( function() {
				$( this ).selectize();
			} );
		} );

	} );

} )(jQuery);
