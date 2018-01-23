(function($) {

	$( document ).ready( function() {

		$( '.pretty-select' ).selectize();

		// Copy merge tag

		var merge_tag_clipboard = new Clipboard('#notification_merge_tags .inside ul li code');

		merge_tag_clipboard.on('success', function(e) {

		    var $code = $(e.trigger),
			    tag   = $code.text();

			$code.text( notification.copied );

			setTimeout(function() {
				$code.text( tag );
			}, 800);

		});

		// Get fresh merge tags for selected trigger

		$('#notification_trigger_select').selectize().change(function() {

			var $select    = $(this),
				$container = $('#notification_merge_tags .inside'),
				$metabox   = $('#notification_merge_tags');

			$metabox.fadeTo(200, 0.5);

			var data = {
				'action': 'notification_get_merge_tags',
				'trigger': $select.val()
			};

		    $.post(ajaxurl, data, function(response) {

		    	if ( response.success == false ) {
		    		$container.html( '<p>' + response.data + '</p>' );
		    		var tags = {};
		    	} else {

					ul = $("<ul>");

					var tags = response.data;

					$.each(tags, function( n, tag ) {
						ul.append('<li><code data-clipboard-text="{' + tag + '}">{' + tag + '}</code></li>');
					});

					$container.html(ul);

		    	}

		    	wp.hooks.doAction( 'notification.changed_trigger', $select.val(), tags );

		    	$metabox.fadeTo(200, 1);

			});

		});

		// Update recipient rows: number and remove button

		var update_recipients = function() {

			var i = 0;

			if ( $('#notification_recipients .recipient').length == 1 ) {
				var disable = true;
			} else {
				var disable = false;
			}

			$('#notification_recipients .recipient').each( function() {

				var $row           = $(this),
					$remove_button = $row.find('.actions .dashicons-trash'),
					$inputs        = $row.find('input, select');

				if ( disable ) {
					$remove_button.addClass('disabled');
				} else {
					$remove_button.removeClass('disabled');
				}

				$inputs.each( function() {

					var $input    = $(this),
						part_name = $input.attr('name');

					$input.attr( 'name', part_name.replace( /notification_recipient\[[0-9]+\]/, 'notification_recipient['+i+']' ) );

				} );

				i++;

			} );

		}

		// Add recipient

		var add_recipient = function( type, value ) {

			type  = typeof type !== 'undefined' ? type : '';
			value = typeof value !== 'undefined' ? value : '';

			var $container = $('#notification_recipients .recipients');

			$container.fadeTo(200, 0.5);

			var data = {
				'action': 'notification_add_recipient',
				'type':   type,
				'value':  value
			};

		    $.post(ajaxurl, data, function(response) {

		    	if ( response.success == false ) {
		    		alert( response.data );
		    	} else {

		    		var $row = $(response.data);
					$container.append($row);

					// update inputs data when values are dynamic
					$row.find('input, select').each( function() {
						wp.hooks.doAction( 'notification.update_input', $(this), $(this).data('value'), $(this).data('update') );
					} );

		    	}

		    	$container.fadeTo(200, 1);

		    	update_recipients();

			});

		};

		$('#notification_recipients').on( 'click', '#notification_add_recipient', function( event ) {

			event.preventDefault();

			add_recipient();

		});

		// Remove recipient

		$('#notification_recipients').on( 'click', '.recipient .actions .dashicons-trash', function( event ) {

			event.preventDefault();

			var $button    = $(this),
				$container = $button.parents('.recipient').first();

			if ( $button.hasClass('disabled') ) {
				return false;
			}

			$container.animate({left: '50px', opacity: 0}, 400, 'linear', function() {
	            $(this).remove();
	            update_recipients();
	        } );

		});

		// Get input field for recipient

		$('#notification_recipients').on( 'change', '.recipient .group select', function() {

			var $select    = $(this),
				$input     = $select.parent().next('.value').find('input, select').first(),
				$container = $input.parent();

			$container.fadeTo(200, 0.5);

			var data = {
				'action': 'notification_get_recipient_input',
				'recipient_name': $select.val()
			};

		    $.post(ajaxurl, data, function(response) {

		    	if ( response.success == false ) {
		    		alert( response.data );
		    	} else {

		    		var $input = $(response.data);
					$container.html($input);

					if ( typeof $input.data('update') !== 'undefined' ) {
						wp.hooks.doAction( 'notification.update_input', $input, $input.data('value'), $input.data('update') );
					}

		    	}

		    	$container.fadeTo(200, 1);

		    	update_recipients();

			});

		});

		// Process all recipient fields which needs live update

		var update_all_inputs = function() {

			$('#notification_recipients .recipient').each( function() {

				var $input = $(this).find('.field.value select, .field.value input');

				if ( typeof $input.data('update') !== 'undefined' ) {
					wp.hooks.doAction( 'notification.update_input', $input, $input.data('value'), $input.data('update') );
				}

			} );

		};

		$(document).ready( update_all_inputs );
		wp.hooks.addAction( 'notification.changed_trigger', update_all_inputs );

		// Update merge tags input

		wp.hooks.addAction( 'notification.update_input', function( $input, value, update_type ) {

			if ( update_type == 'email_merge_tags' ) {

				var trigger    = $('#notification_trigger_select').val(),
					$container = $input.parent();

				$container.fadeTo(200, 0.5);

				var data = {
					'action':  'notification_get_email_merge_tags',
					'trigger': trigger
				};

			    $.post(ajaxurl, data, function(response) {

			    	if ( response.success == false ) {
			    		alert( response.data );
			    	} else {

			    		// clear options first
			    		$input.find('option').remove();

			    		// add empty option for clarity
			    		$input.append( $('<option>') );

			    		$.each(response.data, function(tag_value, tag_name) {

			    			var atts = {
						        value: tag_value,
						        text : tag_name
						    };

						    if ( tag_value == value ) {
						    	atts.selected = 'selected';
						    }

						    $input.append( $('<option>', atts) );

						});

			    	}

			    	$container.fadeTo(200, 1);

				});

			}

		} );

		// Update notification defaults: title, template and recipients

		wp.hooks.addAction( 'notification.changed_trigger', function( trigger_slug, tags ) {

			if ( typeof tinymce.editors.content == 'undefined' ) {
				var editor_content = $('#content').val();
			} else {
				var editor_content = tinymce.activeEditor.getContent();
			}

			if ( editor_content == '' ) {

				var data = {
					'action':  'notification_get_defaults',
					'trigger': trigger_slug
				};

				$.post(ajaxurl, data, function(response) {

			    	if ( response.success == false ) {
			    		alert( response.data );
			    	} else {

			    		var defaults = response.data;

			    		if ( defaults.title ) {
			    			$( '#title' ).val( defaults.title ).focus();
			    		}

			    		if ( defaults.template ) {

			    			if ( typeof tinymce.editors.content == 'undefined' ) {
								$('#content').val( defaults.template );
							} else {
								tinymce.editors.content.setContent( defaults.template );
							}

			    		}

			    		if ( defaults.recipients ) {

			    			$('#notification_recipients .recipients .recipient').first().remove();

			    			$.each( defaults.recipients, function( type, value ) {
								add_recipient( type, value );
							} );

			    		}

			    	}

				});

			}

		} );

		// Dismiss beg message

		$( '.notification-notice' ).on( 'click', '.dismiss-beg-message', function( event ) {

			event.preventDefault();

			var $button = $( this );

			var data = {
				'action':  'notification_dismiss_beg_message',
				'nonce': $button.data( 'nonce' )
			};

			$.post( ajaxurl, data, function( response ) {
		    	$button.parents( '.notification-notice' ).slideUp();
			});

		});

		// Dismiss beg email message

		$( '.notification-notice' ).on( 'click', '.dismiss-beg-email-message', function( event ) {

			event.preventDefault();

			var $button = $( this );

			var data = {
				'action':  'notification_dismiss_beg_email_message',
				'nonce': $button.data( 'nonce' )
			};

			$.post( ajaxurl, data, function( response ) {
		    	$button.parents( '.notification-notice' ).slideUp();
			});

		});

		// Plugin removal

		$( '#the-list tr[data-slug="notification"] .deactivate a' ).click( function( event ) {
			event.preventDefault();
		} );

		$( 'body' ).on( 'submit', '#notification-plugin-feedback-form', function( event ) {

			event.preventDefault();

			var $form = $(this);

			$form.find( '.spinner' ).addClass( 'is-active' );

			var data = {
				'action': 'notification_send_feedback',
				'form'  : $form.serializeArray()
			};

			$.post( ajaxurl, data, function( response ) {
				window.location.href = $( '#the-list tr[data-slug="notification"] .deactivate a' ).data( 'deactivate' );
			});

		} );

	} );

})(jQuery);
