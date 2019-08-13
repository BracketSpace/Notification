(function($) {

	$( document ).ready( function() {

		var recipient_type_select = '.recipients-repeater select.recipient-type';

		// Changed recipient type
		$( 'body' ).on( 'change', recipient_type_select, function() {
			var $select    = $( this ),
				$recipient = $select.parent().parent().next().find( '.recipient-value' ).first(),
				type       = $select.val(),
				$table     = $select.parents( '.recipients-repeater' ).first();

			wp.hooks.doAction( 'notification.carrier.recipients.type.changed', type, $select, $recipient, $table );
 		} );

		// Get recipient field according to type

		wp.hooks.addAction( 'notification.carrier.recipients.type.changed', 'notification', function( type, $select, $recipient, $table ) {

			data = {
				action     : 'get_recipient_input',
				type       : type,
				carrier    : $table.data( 'carrier' ),
				input_name : $recipient.attr( 'name' )
			}

			$recipient.attr( 'disabled', true );

			$.post( notification.ajaxurl, data, function( response ) {

		    	if ( response.success == true ) {
		    		var $replacement         = $( response.data ),
		    			$recipient_container = $recipient.parent();
		    		$recipient_container.html( '' );
		    		$recipient_container.append( $replacement );
		    		wp.hooks.doAction( 'notification.carrier.recipients.recipient.replaced', $replacement );
		    	}

			} );

		} );

	} );

})(jQuery);
