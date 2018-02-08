(function($) {

	$( document ).ready( function() {

		var recipient_type_select = '.recipients-repeater select.recipient-type';

		// Changed recipient type
		$( 'body' ).on( 'change', recipient_type_select, function() {
			var $select    = $( this ),
				$recipient = $select.parent().next().find( '.recipient-value' ).first(),
				type       = $select.val(),
				$table     = $select.parents( '.recipients-repeater' ).first();

			wp.hooks.doAction( 'notification.recipients.type.changed', type, $select, $recipient, $table );
 		} );

		// Get recipient field according to type

		wp.hooks.addAction( 'notification.recipients.type.changed', function( type, $select, $recipient, $table ) {

			data = {
				action      : 'get_recipient_input',
				type        : type,
				notification: $table.data( 'notification' ),
				input_name  : $recipient.attr( 'name' )
			}

			$recipient.attr( 'disabled', true );

			$.post( notification.ajaxurl, data, function( response ) {

		    	if ( response.success == true ) {
		    		$recipient.replaceWith( response.data );
		    	}

			} );

		} );

	} );

})(jQuery);
