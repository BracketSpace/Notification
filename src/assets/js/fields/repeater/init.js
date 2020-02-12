/* global notification, */

export const init = {
	mounted(){
		this.setType();
		this.apiCall();
	},
	methods: {
		apiCall(){
			this.postID = notification.postId;

			fetch( `http://notification.local/wp-json/notification/v2/repeater-field/${this.postID}`,{
				method: 'POST',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json'
				},
				body: JSON.stringify( this.type )
			} )
			.then( res => res.json() )
			.then( data => {
				const configuration = JSON.parse( data );

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
	}
}
