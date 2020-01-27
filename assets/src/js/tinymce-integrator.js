class TinyMceIntegrator {

	constructor(){
		if( 'notification' === pagenow && 'notification' === typenow ) {
			this.init();
			this.mergeTagCompatibility();
		}
	}

	init(){
		tinymce.PluginManager.add('notification-tiny-mce-extension', (editor, url) => {
			if (editor) {
				editor.addCommand('WP_Link', function () {
					window.wpLink.open(editor.id);
				});

			}
		});
	}

	mergeTagCompatibility(){
		originalWpLink = Object.assign({}, wpLink);

		wpLink = _.extend( wpLink, {

			getAttrs: () => {

				const attrs = originalWpLink.getAttrs();
				let href = attrs.href;

				if( ! wpLink.isMergeTag( href ) ) {
					return attrs;
				} else {
					href = href.replace( /^(http?:|)\/\// , '' );
				}

				attrs.href = href;

				return attrs;
			},

			correctURL: function(){

				if( ! wpLink.isMergeTag( this.value ) ) {
					originalWpLink.correctURL();
				} else {
					return;
				}
			},
			isMergeTag: ( href ) => {

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
}

jQuery(function () {

	new TinyMceIntegrator();

});
