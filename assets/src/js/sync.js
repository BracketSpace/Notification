( function($) {

	var __ = wp.i18n.__;

	$( document ).ready( function() {

		$( '.group-sync .field-actions .notification-sync-all' ).on( 'click', function( event ) {

			event.preventDefault();

			var $master_button = $( this );

			if ( $master_button.attr( 'disabled' ) ) {
 				return false;
 			}

			$master_button.attr( 'disabled', true );

			$( '.group-sync .field-notifications tr' ).each( function( num, notification_row ) {

				var $button = $( notification_row ).find( '.button.notification-sync' );

				if ( $button.data( 'sync-type' ) === $master_button.data( 'type' ) ) {
					wp.hooks.doAction( 'notification.sync.init', $button );
				}

			} );

 		} );

		$( '.group-sync .field-notifications td > .button.notification-sync' ).on( 'click', function( event ) {
			event.preventDefault();
			wp.hooks.doAction( 'notification.sync.init', $( this ) );
 		} );

 		wp.hooks.addAction( 'notification.sync.init', 'notification', function( $button ) {

 			if ( $button.attr( 'disabled' ) ) {
 				return false;
 			}

 			var sync_type = $button.data( 'sync-type' ),
 				hash      = $button.data( 'sync-hash' ),
 				nonce     = $button.data( 'nonce' ),
 				label     = $button.text();

			$button.attr( 'disabled', true );
			$button.text( __( 'Synchronizing...', 'notification' ) );

 			data = {
				action : 'notification_sync',
				hash   : hash,
				type   : sync_type,
				nonce  : nonce
			}

			$.post( notification.ajaxurl, data, function( response ) {

				if ( response.success == true ) {

					var $notification_row = $button.parent().parent();

					if ( 'wordpress' === sync_type ) {
						var $title_td = $notification_row.find( 'td.title' );
						var $link     = $( '<a>', {
								text : $title_td.text(),
								href : response.data
							} );
						$title_td.html( $link );
					}

					$notification_row.find( 'td.status' ).text( __( 'Synchronized', 'notification' ) );
					$button.remove();

				} else {
					alert( response.data );
				}

				$button.attr( 'disabled', false );

			} );

 		} );

	} );

} )(jQuery);
