// /* eslint no-alert: 0 */
// /* global notification, jQuery */
// ( function( $ ) {
// 	$( document ).ready( function() {
// 		$( '.notification-pretty-select:visible' ).selectize();

// 		notification.hooks.addAction( 'notification.carrier.recipients.recipient.replaced', function( $input ) {
// 			if ( $input.hasClass( 'notification-pretty-select' ) ) {
// 				$input.selectize();
// 			}
// 		} );

// 		notification.hooks.addAction( 'notification.repeater.row.added', function( $row ) {
// 			$row.find( 'select.notification-pretty-select' ).each( function() {
// 				$( this ).selectize();
// 			} );
// 		} );
// 	} );
// }( jQuery ) );
