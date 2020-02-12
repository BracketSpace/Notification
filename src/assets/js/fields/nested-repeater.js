/* global Vue, notification, */


document.addEventListener('DOMContentLoaded', () => {

	const postID = notification.postId;
	const vueWrappers = document.querySelectorAll( '.vue-repeater' );
	const vueInstances = {};

	for( const wrapper of vueWrappers ){
		const wrapperId = wrapper.getAttribute( 'id' );

		vueInstances[ wrapperId ] = new Vue( {
			el: `#${wrapperId}`,
			data: {
				'model' : '',
				'type' : {},
				'fields': [],
				'rowCount': 0,
				'values': []
			},
			mounted(){
				this.setType();
				this.init();
			},
			methods: {
				init(){

					fetch( `http://notification.local/wp-json/notification/v2/repeater-field/${postID}`,{
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
				addModel( field ){
					this.model = this.cloneField( field );
				},
				addFields(){
					if( this.rowCount ){
						for( let i = 0; i < this.rowCount; i++ ){
							const model = this.cloneField( this.model );

							this.fields.push( model );
 						}
					} else {
						const model = this.cloneField( this.model );

						this.fields.push( model );
						this.rowCount++;
					}
				},
				cloneField( model ){
					const clonedModel = [];

					model.forEach( element => {
						const field = Object.assign( {}, element );
						clonedModel.push( field );
					});

					return clonedModel;
				},
				addField( event ){
					event.preventDefault();

					const model = this.cloneField( this.model );

					this.rowCount++;
					this.fields.push(model);
					notification.hooks.doAction( 'notification.repeater.row.added', model, this );

				},
				removeField( index ){
					this.fields.splice( index, 1 );
					notification.hooks.doAction( 'notification.repeater.row.removed', this );

				},
				addFieldValues(){

					for( let i = 0; i <= this.rowCount; i++ ){
						let counter = 0;

						for( const value in this.values[i] ){
							this.fields[i][counter].value = this.values[i][value];
							counter++;
						}
					}
				}
			}
		} )
	}
});
