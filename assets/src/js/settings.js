( function( $ ) {

	$(document).ready( function() {
		$( '.underdev-settings .pretty-select' ).selectize();
		new jQueryCollapse( $( '.underdev-settings .setting-group' ), {
			open: function() {
				this.slideDown(100);
			},
			close: function() {
				this.slideUp(100);
			}
		} );
		$( '.setting-group-header' ).click( function() {
			var wrapper = $(this).parents('.setting-group').find( '.form-table' );
			wrapper.trigger( 'toggle' );
		} );
	} );

} )( jQuery );
