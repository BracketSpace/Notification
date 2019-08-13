/* eslint no-alert: 0 */
/* global notification, wp, jQuery */
( function( $ ) {
	const __ = wp.i18n.__;

	$( document ).ready( function() {
		const recalculateRows = function( $repeater ) {
			let i = 0;

			$repeater.find( '.row:not(.header):not(.model)' ).each( function() {
				const $row = $( this ),
					$inputs = $row.find( '.notification-field:not(.selectize-control):not(.selectize-dropdown)' );

				$inputs.each( function() {
					const $input = $( this ),
						partName = $input.attr( 'name' );

					$input.attr( 'name', partName.replace( /(.*)\[([0-9]+)\]/, '$1[' + i + ']' ) );
				} );

				i++;
			} );
		};

		// Set model in disable state
		$( '.fields-repeater .model .subfield' ).each( function() {
			$( this ).find( 'input, textarea, select' ).attr( 'disabled', true );
		} );

		// Remove row
		function removeRow( $removeButton ) {
			const $repeater = $removeButton.parents( '.fields-repeater' ).first();

			$removeButton.parents( '.row' ).first().animate( { opacity: 0 }, 400, 'linear', function() {
				$( this ).remove();
				recalculateRows( $repeater );
				wp.hooks.doAction( 'notification.repeater.row.removed', $repeater );
			} );
		}

		$( '.fields-repeater' ).on( 'click', '.row:not(.header):not(.model) .trash', function() {
			const $removeButton = $( this );

			if ( $( window ).width() > 768 ) {
				removeRow( $removeButton );
			} else if ( window.confirm( __( 'Do you really want to delete this?', 'notification' ) ) ) {
				removeRow( $removeButton );
			}
		} );

		// Add row
		$( '.add-new-repeater-field' ).on( 'click', function( event ) {
			event.preventDefault();

			const $repeater = $( this ).prev( '.fields-repeater' ),
				$model = $repeater.find( '.row.model' ),
				$cloned = $model.clone().removeClass( 'model' );

			$cloned.find( 'input, textarea, select' ).attr( 'disabled', false );
			$cloned.appendTo( $repeater );

			recalculateRows( $repeater );

			notification.hooks.doAction( 'notification.repeater.row.added', $cloned, $repeater );
		} );

		// Sortable
		$( '.fields-repeater-sortable > tbody' ).sortable( {
			handle: '.handle',
			axis: 'y',
			start( e, ui ) {
				ui.placeholder.height( ui.helper[ 0 ].scrollHeight );
			},
		} );
	} );
}( jQuery ) );
