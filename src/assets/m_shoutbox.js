/*!
	Shoutbox
	(c) 2011-2013 Martin Kozianka <http://kozianka.de>
*/

var Shoutbox = (function() {

return {

	refresh: function(id) {
		var action = $(id).getElement('form').get('action');
		if ((typeof action) !== 'string') {
			action ='';
		}

		new Request({
			onSuccess: function(htmlString) {
				if (htmlString.length > 0) {
                    $(id + '_list').set('html', htmlString);
                    // Scroll to top after refresh
                    new Fx.Scroll(id + '_entries').toTop();
				}
			},
			url: action + '?shoutbox_action=update',
			method:'get'
		}).send(); // 'shoutbox_action=update'
		
		
	},

	insertAtCursor: function(myField, myValue) {

		if (document.selection) {
			// IE
			myField.focus();
			sel = document.selection.createRange();
			sel.text = myValue;
		}
		
		else if (myField.selectionStart || myField.selectionStart == '0') {
			// Mozilla
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;
			myField.value = myField.value.substring(0, startPos)
			+ myValue
			+ myField.value.substring(endPos, myField.value.length);
		} else {
			myField.value += myValue;
		}
	},
	
		
	init: function(shoutbox_id) {
		// add ajax parameter and submit event
		var theForm = $(shoutbox_id).getElement('form');

		var action = theForm.get('action');
		if ((typeof action) !== 'string') {
			action ='';
		}

		$$('div.smiley_legend ul.smiley_list span').addEvent('click', function() {
    			Shoutbox.insertAtCursor(document.getElementById(shoutbox_id+'_textarea'), ' '+this.title);
		});
		
		theForm.addEvent('submit', function(e) {
			// Prevent the submit event
			e.stop();

			$$('#'+shoutbox_id+' button.submit').set('disabled', 'disabled');

			if ($(shoutbox_id).getElement('textarea').get('value').length == 0) {
				$$('#'+shoutbox_id+' button.submit').set('disabled', null);
				return false;
			}


			new Request({
				onSuccess: function(jsonString) {
					var jsonObj = JSON.decode(jsonString);
										
					$$('#'+shoutbox_id+' input.request_token').set('value', jsonObj.token);

                    $(shoutbox_id + '_list').set('html', jsonObj.entriesHtml);
                    new Fx.Scroll(shoutbox_id + '_entries').toTop();

                    if (jsonObj.addedEntry) {
                        // clear textarea
                        $(shoutbox_id).getElement('textarea').set('value', '');
                    }
                    else {
                        // show message
                        alert(jsonObj.message);
                    }

                    $$('#'+shoutbox_id+' button.submit').set('disabled', null);
				},
				method: 'post',
				url:    action
			}).send(theForm.toQueryString());
 
 		
		});
	}

};


})();
