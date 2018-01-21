(function($) {

	$( document ).ready( function() {

		// Copy merge tag

		var merge_tag_clipboard = new Clipboard('#notification_merge_tags .inside ul li code');

		merge_tag_clipboard.on('success', function(e) {

		    var $code = $(e.trigger),
			    tag   = $code.text();

			wp.hooks.doAction( 'notification.merge_tag.copied', tag, $code );

			$code.text( notification.copied );

			setTimeout(function() {
				$code.text( tag );
			}, 800);

		});

		// Swap merge tags list for new trigger

		wp.hooks.addAction( 'notification.trigger.changed', function( $trigger ) {

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

	} );

})(jQuery);
