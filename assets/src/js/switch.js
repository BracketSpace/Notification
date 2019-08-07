( function($) {

	$( document ).ready( function() {

		$( '.column-switch .onoffswitch' ).on( 'click', function( event ) {

			var $switch = $( this ),
				post_id = $switch.data( 'postid' );

			event.preventDefault();

			wp.hooks.doAction( 'notification.status.changed', $switch, post_id );

 		} );

 		wp.hooks.addAction( 'notification.status.changed', 'notification', function( $switch, post_id ) {

 			var status = ! $switch.find( 'input' ).attr( 'checked' );

 			$switch.addClass( 'loading' );

 			data = {
				action : 'change_notification_status',
				post_id: post_id,
				status : status,
				nonce  : $switch.data( 'nonce' )
			}

			$.post( notification.ajaxurl, data, function( response ) {

		    	if ( response.success == true ) {
		    		$switch.removeClass( 'loading' );
	 				$switch.find( 'input' ).attr( 'checked', status );
		    	} else {
		    		alert( response.data );
		    	}

			} );

 		} );

	} );

} )(jQuery);
