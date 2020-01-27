jQuery(function ( $ ) {

	if( 'notification' === pagenow && 'notification' === typenow ) {

		let input = $( '#wp-link-url' );

		tinymce.PluginManager.add('notification-tiny-mce-extension', function (editor, url) {
			if (editor) {
				editor.addCommand('WP_Link', function () {
					window.wpLink.open(editor.id);
				});

			}
		});

		originalWpLink = _.clone( wpLink );

		wpLink = _.extend( wpLink, {

			getAttrs: function() {
				const attrs = originalWpLink.getAttrs();
				let href = attrs.href;

				if( ! this.isMergeTag( href ) ) {
					return attrs;
				} else {
					href = href.replace( /^(http?:|)\/\// , '' );
				}

				attrs.href = href;

				return attrs;
			},

			correctURL: function() {

				if( ! wpLink.isMergeTag( input[0].value ) ) {
					originalWpLink.correctURL();
				} else {
					return;
				}
			},

			isMergeTag: function( href ){

				if( undefined !== href ) {
					if ( -1 === href.search( '{' ) ) {
						return false;
					} else {
						return true;
					}
				}
			},

		});
	}

});
