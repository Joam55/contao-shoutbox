/*!
 Shoutbox
 (c) 2011-2013 Martin Kozianka <http://kozianka.de>
 */

var Shoutbox = (function() {

    return {

        refresh: function(sbId) {
            var shoutbox_id = '#' + sbId;
            var action      = $(shoutbox_id+' form').attr('action');

            if ((typeof action) !== 'string') {
                action ='';
            }

            $.get(action + '?shoutbox_action=update', function(htmlString, textStatus, jqXHR) {
                if (htmlString.length > 0) {
                    $(shoutbox_id + '_list').html(htmlString);
                    // Scroll to top after refresh
                    $(shoutbox_id + '_entries').scrollTop(0);
                }

            }, 'html');


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


        init: function(sbId) {
            var shoutbox_id = '#' + sbId;
            // add ajax parameter and submit event
            var theForm = $(shoutbox_id+' form');


            var action = theForm.get('action');
            if ((typeof action) !== 'string') {
                action ='';
            }

            $('div.smiley_legend ul.smiley_list span').bind('click', function() {
                Shoutbox.insertAtCursor(document.getElementById(sbId+'_textarea'), ' '+this.title);
            });

            theForm.bind('submit', function(e) {
                // Prevent the submit event
                e.preventDefault();



                $(shoutbox_id+' button.submit').attr('disabled', 'disabled');
                $(shoutbox_id+' .submit_layer').show();

                if ($(shoutbox_id+'_textarea').val().length == 0) {
                    $(shoutbox_id+' button.submit').attr('disabled', null);
                    $(shoutbox_id+' .submit_layer').hide();
                    return false;
                }

                $.post(action, theForm.serialize(), function(jsonObj, textStatus, jqXHR) {

                    $(shoutbox_id+' input.request_token').attr('value', jsonObj.token);


                    $(shoutbox_id + '_list').html(jsonObj.entriesHtml);
                    $(shoutbox_id + '_entries').scrollTop(0);

                    if (jsonObj.addedEntry) {
                        // clear textarea
                        $(shoutbox_id +'_textarea').val('');
                    }
                    else {
                        // show message
                        alert(jsonObj.message);
                    }

                    $(shoutbox_id+' button.submit').attr('disabled', null);
                    $(shoutbox_id+' .submit_layer').hide();
                }, 'json');


            });
        }

    };


})();
