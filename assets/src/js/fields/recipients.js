/* global notification, jQuery */
( function( $ ) {
	$( document ).ready( function() {
		const recipientTypeSelect = '.recipients-repeater select.recipient-type';

		// Changed recipient type
		$( 'body' ).on( 'change', recipientTypeSelect, function() {
			const $select = $( this ),
				$recipient = $select.parent().parent().next().find( '.recipient-value' ).first(),
				type = $select.val(),
				$table = $select.parents( '.recipients-repeater' ).first();

			notification.hooks.doAction( 'notification.carrier.recipients.type.changed', type, $select, $recipient, $table );
		} );

		// Get recipient field according to type

		notification.hooks.addAction( 'notification.carrier.recipients.type.changed', 'notification', function( type, $select, $recipient, $table ) {
			const data = {
				action: 'get_recipient_input',
				type,
				carrier: $table.data( 'carrier' ),
				input_name: $recipient.attr( 'name' ),
			};

			$recipient.attr( 'disabled', true );

			$.post( notification.ajaxurl, data, function( response ) {
				if ( response.success === true ) {
					const $replacement = $( response.data ),
						$recipientContainer = $recipient.parent();

					$recipientContainer.html( '' );
					$recipientContainer.append( $replacement );
					notification.hooks.doAction( 'notification.carrier.recipients.recipient.replaced', $replacement );
				}
			} );
		} );
	} );
}( jQuery ) );
