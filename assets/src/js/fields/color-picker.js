(function($) {

	$( document ).ready( function() {
		$( '.notification-color-picker:visible' ).wpColorPicker();
	} );

	wp.hooks.addAction( 'notification.carrier.repeater.row.added', 'notification', function( $cloned, $repeater ) {
		$cloned.find( '.notification-color-picker' ).wpColorPicker();
	} );

})(jQuery);
