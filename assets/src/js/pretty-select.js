( function($) {

	$( document ).ready( function() {

		$( '.notification-pretty-select:visible' ).selectize();

		notification.hooks.addAction( 'notification.recipients.recipient.replaced', function( $input ) {
			if ( $input.hasClass( 'notification-pretty-select' ) ) {
				$input.selectize();
			}
		} );

		notification.hooks.addAction( 'notification.notification.repeater.row.added', function( $row, $repeater ) {
			$row.find( 'select.notification-pretty-select' ).each( function() {
				$( this ).selectize();
			} );
		} );

	} );

} )(jQuery);
