/*eslint-disable no-undef */
class TinyMceIntegrator {
	constructor() {
		if ( 'notification' === pagenow && 'notification' === typenow ) {
			this.init();
			this.mergeTagCompatibility();
		}
	}

	init() {
		tinymce.PluginManager.add( 'notification-tiny-mce-extension', ( editor ) => {
			if ( editor ) {
				editor.addCommand( 'WP_Link', function() {
					window.wpLink.open( editor.id );
				} );
			}
		} );
	}

	mergeTagCompatibility() {
		const originalWpLink = Object.assign( {}, wpLink );

		wpLink = _.extend( wpLink, {

			getAttrs: () => {
				const attrs = originalWpLink.getAttrs();
				let href = attrs.href;

				if ( ! wpLink.isMergeTag( href ) ) {
					return attrs;
				}
				href = href.replace( /^(http?:|)\/\//, '' );

				attrs.href = href;

				return attrs;
			},

			correctURL() {
				if ( undefined === this.value ) {
					return;
				}

				if ( wpLink.isMergeTag( this.value ) ) {
					return;
				}

				originalWpLink.correctURL();
			},

			isMergeTag: ( href ) => {
				if ( -1 === href.search( '{' ) ) {
					return false;
				}
				return true;
			},

		} );
	}
}

jQuery( function() {
	new TinyMceIntegrator();
} );

/* eslint-enable */
