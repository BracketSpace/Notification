( function($) {

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

} )(jQuery);
