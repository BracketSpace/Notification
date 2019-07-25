( function($) {

	var __ = wp.i18n.__;

	$( document ).ready( function() {

		var $button = $( '#export-notifications .button' );
		var $items  = $( '#export-notifications ul li input[type="checkbox"]:not(.select-all)' );
		var link    = $button.prop( 'href' );

		function get_selected_items() {
			var items = [];
			$.each( $items, function( index, item ) {
				$item = $( item );
				if ( $item.is( ':checked' ) ) {
					items.push( $item.val() );
				}
			} );
			return items.join();
		}

		$( '#export-notifications input[type="checkbox"]' ).change( function() {

			if ( $( this ).hasClass( 'select-all' ) ) {

				if ( $( this ).is( ':checked' ) ) {
					$items.prop( 'checked', true );
				} else {
					$items.prop( 'checked', false );
				}

			}

			$button.prop( 'href', link + get_selected_items() );

		} );

	} );

	$( document ).ready( function() {

		var $button  = $( '#import-notifications .button' );
		var $file    = $( '#import-notifications input[type="file"]' );
		var files    = [];
		var $message = $( '#import-notifications .message' );

		function clear_message() {
			$message.removeClass( 'success' ).removeClass( 'error' ).text( '' );
		};

		function add_message( type, message ) {
			clear_message();
			$message.addClass( type ).text( message );
		};

		$file.on( 'change', function( event ) {
			files = event.target.files;
			$.each( files, function( key, value ) {
				if ( 'application/json' !== value.type ) {
					add_message( 'error', __( 'Please upload only valid JSON files', 'notification' ) );
					$file.val( '' );
				} else {
					clear_message();
				}
			} );
		} );

		$button.on( 'click', function( event ) {

			if ( 'true' === $button.data( 'processing' ) ) {
				return false;
			}

			event.preventDefault();

			var data = new FormData();
			$.each( files, function( key, value ) {
				data.append( key, value );
			} );

			data.append( 'action', 'notification_import_json' );
			data.append( 'type', 'notifications' );
			data.append( 'nonce', $button.data( 'nonce' ) );

			add_message( 'neutral', __( 'Importing data...', 'notification' ) );
			$button.data( 'processing', 'true' );

			$.ajax( {
		        url: notification.ajaxurl,
		        type: 'POST',
		        data: data,
		        cache: false,
		        dataType: 'json',
		        processData: false,
		        contentType: false,
		        success: function( response ) {
		        	if ( response.success ) {
		        		add_message( 'success', response.data );
		        		$file.val( '' );
		        	} else {
		        		add_message( 'error', response.data );
		        	}
		        	$button.data( 'processing', 'false' );
		        },
		        error: function( jqXHR, text_status, error_thrown ) {
		            add_message( 'error', error_thrown );
		        }
		    } );

		} );

	} );

} )(jQuery);
