/* global notification, jQuery */
( function( $ ) {
	$( document ).ready( function() {
		$( '.notification-color-picker:visible' ).wpColorPicker();
	} );

	notification.hooks.addAction( 'notification.carrier.repeater.row.added', 'notification', function( $cloned ) {
		$cloned.find( '.notification-color-picker' ).wpColorPicker();
	} );
}( jQuery ) );
