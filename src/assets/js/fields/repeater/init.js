/* global jQuery, notification, */

export const init = {
	mounted(){
		this.setType();
		this.apiCall();
		this.sortable();
	},
	methods: {
		apiCall(){
			this.postID = notification.postId;

			fetch( `http://notification.local/wp-json/notification/v1/repeater-field/${this.postID}`,{
				method: 'POST',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json'
				},
				body: JSON.stringify( this.type )
			} )
			.then( res => res.json() )
			.then( data => {
				const configuration = data;

				this.addModel( configuration.field );

				if(configuration.values ){
					this.values = configuration.values
					this.rowCount = this.values.length;
					this.addFields();
					this.addFieldValues();
				}
			}
			);

		},
		setType(){
			const instance = this.$el;
			const fieldType = instance.getAttribute( 'data-field-name' );
			const fieldCarrier = instance.getAttribute( 'data-carrier' );

			this.type = {
				fieldType,
				fieldCarrier
			}
		},
		sortable(){
			jQuery( '.fields-repeater-sortable > tbody' ).sortable( {
				handle: '.handle',
				axis: 'y',
				start( e, ui ) {
					ui.placeholder.height( ui.helper[ 0 ].scrollHeight );
				},
			} );
		}
	}
}
