(function($) {

	$( document ).ready( function() {

		var $image_field   = $( '.notification-image-field' );
		var clicked_image_field = false;

		$( 'body' ).on( 'click', '.notification-image-field .image .preview, .notification-image-field .select-image', function( event ) {
			event.preventDefault();

			$clicked_image_field = $(this).parents( '.notification-image-field' );

			if ( wp.media.frames.frame ) {
				wp.media.frames.frame.open();
				return;
			}

			wp.media.frames.frame = wp.media( {
				title: notification.i18n.select_image,
				multiple: false,
				library: {
					type: 'image'
				},
				button: {
					text: notification.i18n.use_selected_image
				}
			} );

			var media_set_image = function() {
				var selection = wp.media.frames.frame.state().get( 'selection' );

				if ( ! selection ) {
					return;
				}

				selection.each( function( attachment ) {
					$clicked_image_field.addClass( 'selected' );
					$clicked_image_field.find( '.image-input' ).val( attachment.id );
					$clicked_image_field.find( '.image .preview' ).attr( 'src', attachment.attributes.sizes.thumbnail.url );
				} );
			};

			wp.media.frames.frame.on( 'select', media_set_image );
			wp.media.frames.frame.open();
		} );

		$image_field.find( '.image .clear' ).on( 'click', function( event ) {
			event.preventDefault();
			$( this ).parents( '.notification-image-field' ).removeClass( 'selected' );
			$( this ).parents( '.notification-image-field' ).find( '.image-input' ).val( '' );
			$( this ).parents( '.notification-image-field' ).find( '.image .preview' ).attr( 'src', '' );
		} );

	} );

})(jQuery);
