(function($) {

	$( document ).ready( function() {

		// Copy merge tag

		var merge_tag_clipboard = new Clipboard( 'code.notification-merge-tag' );

		merge_tag_clipboard.on('success', function(e) {

		    var $code = $(e.trigger),
			    tag   = $code.text();

			notification.hooks.doAction( 'notification.merge_tag.copied', tag, $code );

			$code.text( notification.i18n.copied );

			setTimeout(function() {
				$code.text( tag );
			}, 800);

		});

		// Swap merge tags list for new trigger

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
		    	}

			} );

		} );

		// Search for merge tags

		$( 'body' ).on( 'keyup', '#notification-search-merge-tags', function() {

			var val = $( this ).val().toLowerCase();
			$( '.inside ul li' ).hide();

			$( '.inside ul li ').each( function() {

				var text = $( this ).find( '.intro code' ).text().toLowerCase();

				if ( text.indexOf(val) != -1 ) {
					$(this).show();
				}

			} );

		} );

	} );

})(jQuery);
