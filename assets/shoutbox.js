/*!
	Shoutbox
	(c) 2011-2012 Martin Kozianka <http://kozianka.de>
*/

var Shoutbox = (function() {

var domainNames = [
  "dortmunder-asche.de",
  "facebook.com",
  "fussball.de",
  "plus.google.com",
  "reviersport.de",
  "twitter.com",
  "youtube.com"];

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
				}
				
				// TODO :: scroll to top after refresh
				
				Shoutbox.updateShoutboxLinkTags(id);			},
			url: action + '?shoutbox_action=update',
			method:'get'
		}).send(); // 'shoutbox_action=update'
		
			},
	
	updateShoutboxLinkTags: function(id) {
		onclick_value = "window.open(this.href); return false;";
		$$('#'+id+' a').set('onclick', onclick_value);
		
		
		$$('#'+id+' a img').each(function(img) {
			hrefStr = img.getParent('a').get('href').toLowerCase();
			for(var i in domainNames) {
				if (hrefStr.indexOf(domainNames[i]) != -1) {
					img.set('src', img.get('src').replace('/link.png', '/icons/'+domainNames[i]+'.png'));
				}
			}
			
			
				
		});
			
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
			new Event(e).stop();
			$$('#'+shoutbox_id+' button.submit').set('disabled', 'disabled');

			if ($(shoutbox_id).getElement('textarea').get('value').length == 0) {
				$$('#'+shoutbox_id+' button.submit').set('disabled', null);
				return false;
			}
			new Request({
				onSuccess: function(jsonString) {
					var jsonObj = JSON.decode(jsonString);
										
					$$('#'+shoutbox_id+' input.request_token').set('value', jsonObj.token);					

					// clear textarea
					$(shoutbox_id).getElement('textarea').set('value', '');
					
					Shoutbox.refresh(shoutbox_id);
					$$('#'+shoutbox_id+' button.submit').set('disabled', null);
				},
				method: 'post',
				url: action + '?shoutbox_ajax=true'
			}).send(theForm.toQueryString());
 
 		
		});
	}

};


})();
