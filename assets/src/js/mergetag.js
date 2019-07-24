(function($) {

	var __ = wp.i18n.__;

	$( document ).ready( function() {

		// Copy Merge Tag.
		var merge_tag_clipboard = new Clipboard( 'code.notification-merge-tag' );

		merge_tag_clipboard.on( 'success', function( e ) {

		    var $code = $( e.trigger ),
			    tag   = $code.text();

			notification.hooks.doAction( 'notification.merge_tag.copied', tag, $code );

			$code.text( __( 'Copied', 'notification' ) );

			setTimeout( function() {
				$code.text( tag );
			}, 800 );

		} );

		// Initialize accordion.
		var collapse = $( '.notification_merge_tags_accordion' ).collapse();


		// Swap Merge Tags list for new Trigger.
		notification.hooks.addAction( 'notification.trigger.changed', function( $trigger ) {

			var trigger_slug = $trigger.val();

			data = {
				action       : 'get_merge_tags_for_trigger',
				trigger_slug : trigger_slug
			}

			$.post( ajaxurl, data, function( response ) {

		    	if ( response.success == false ) {
		    		alert( response.data );
		    	} else {
					$( '#notification_merge_tags .inside' ).html( response.data );
					collapse = $( '.notification_merge_tags_accordion' ).collapse();
		    	}

			} );

		} );

		// Search Merge Tags.
		$( 'body' ).on( 'keyup', '#notification-search-merge-tags', function() {

			var val = $( this ).val().toLowerCase();

			if ( $( this ).val().length > 0 ) {

				collapse.trigger( 'open' );

				$( '.notification_merge_tags_accordion h2, .notification_merge_tags_accordion .tags-group' ).hide();

				$( '.inside li' ).each( function () {

					$( this ).hide();

					var text = $( this ).find( '.intro code' ).text().toLowerCase();

					if ( -1 !== text.indexOf( val )) {
						$( this ).show();
						var parentClass = $( this ).parents( 'ul' ).data( 'group' );
						$( '[data-group=' + parentClass + ']' ).show();
					}

				} );

			} else {
				$( '.notification_merge_tags_accordion h2, .inside li' ).show();
				collapse.trigger( 'close' );
			}

		} );

	} );

})(jQuery);
