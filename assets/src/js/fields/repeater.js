(function($) {

	var __ = wp.i18n.__;

	$( document ).ready( function() {

		var recalculate_rows = function( $repeater ) {

			var i = 0;

			$repeater.find( '.row:not(.header):not(.model)' ).each( function() {

				var $row    = $( this ),
					$inputs = $row.find( '.notification-field:not(.selectize-control):not(.selectize-dropdown)' );

				$inputs.each( function() {

					var $input    = $(this),
						part_name = $input.attr('name');

					$input.attr( 'name', part_name.replace( /(.*)\[([0-9]+)\]/, '$1[' + i + ']' ) );

				} );

				i++;

			} );

		}

		// Set model in disable state

		$( '.fields-repeater .model .subfield' ).each( function() {
			$( this ).find( 'input, textarea, select' ).attr( 'disabled', true );
		} );

		// Remove row

		function remove_row( $remove_button ) {

			var $repeater = $remove_button.parents( '.fields-repeater' ).first();

			$remove_button.parents( '.row' ).first().animate( { opacity: 0 }, 400, 'linear', function() {
				$( this ).remove();
	            recalculate_rows( $repeater );
	            wp.hooks.doAction( 'notification.repeater.row.removed', $repeater );
	        } );

		}

		$( '.fields-repeater' ).on( 'click', '.row:not(.header):not(.model) .trash', function() {

			var $remove_button = $( this );

			if ( $( window ).width() > 768 ) {
				remove_row( $remove_button );
			} else if ( window.confirm( __( 'Do you really want to delete this?', 'notification' ) ) ) {
				remove_row( $remove_button );
			}

		} );

		// Add row

		$( '.add-new-repeater-field' ).on( 'click', function( event ) {

			event.preventDefault();

			var $repeater = $( this ).prev( '.fields-repeater' ),
				$model    = $repeater.find( '.row.model' ),
				$cloned   = $model.clone().removeClass( 'model' );

			$cloned.find( 'input, textarea, select' ).attr( 'disabled', false );
			$cloned.appendTo( $repeater )

			recalculate_rows( $repeater );

			wp.hooks.doAction( 'notification.repeater.row.added', $cloned, $repeater );

		} );

		// Sortable

		$( '.fields-repeater-sortable > tbody' ).sortable( {
			handle: '.handle',
			axis: 'y',
	    start:  function( e, ui ) {
	    	ui.placeholder.height( ui.helper[0].scrollHeight );
	    },
		} );

	} );

})(jQuery);
