( function($) {

	$( document ).ready( function() {

		$( '.notification-pretty-select' ).selectize();

		wp.hooks.addAction( 'notification.recipients.recipient.replaced', function( $input ) {
			if ( $input.hasClass( 'notification-pretty-select' ) ) {
				$input.selectize();
			}
		} );

	} );

} )(jQuery);
