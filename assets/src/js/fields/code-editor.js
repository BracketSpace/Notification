/* global wp, notification, jQuery */

( function( $ ) {

	function init_code_editor( $elem ) {
		var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
		editorSettings.codemirror = _.extend( {}, editorSettings.codemirror, $elem.data( 'settings' ) );
		var editor = wp.codeEditor.initialize( $elem, editorSettings );

		notification.hooks.addAction( 'notification.carrier.toggled', () => {
			editor.codemirror.refresh();
		} );
	}

	$( document ).ready( function() {
		$( '.notification-code-editor-field' ).each( function() {
			init_code_editor( $( this ) );
		} );
	} );

}( jQuery ) );
