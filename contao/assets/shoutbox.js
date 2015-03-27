/*!
 Shoutbox
 (c) 2011-2015 Martin Kozianka <http://kozianka.de>
 */

Shoutbox = function(strId) {
    console.log("Shoutbox", strId);
    this.id         = '#'+strId;
    this.form       = $(this.id + ' form');
    this.blockLayer = $(this.id + ' .block_layer');
    this.scrollList = new IScroll(this.id + ' .entries', { mouseWheel: true });

    var sbObj = this;
    this.form.bind('submit', function(e) {
        // Prevent the submit event
        e.preventDefault();

        var $form    = $(this);
        var $txtArea = $('textarea', $form);

        if ($txtArea.val().length === 0) {
            return false;
        }

        $('button', $form).attr('disabled', 'disabled');
        sbObj.blockLayer.show();

        var action = $form.attr('action');
        if ((typeof action) !== 'string') {
            action ='';
        }
        $.post(action, $form.serialize(), function(jsonObj) {

            console.log('jsonObj', jsonObj);
            // new token and update list
            $('input.request_token', $form).attr('value', jsonObj.token);
            $(sbObj.id + ' ul.list').html(jsonObj.entriesHtml);

            if (jsonObj.addedEntry) {
                $('textarea', $form).val('');
            }
            else {
                alert(jsonObj.message);
            }
            $('button', $form).attr('disabled', null);

            sbObj.blockLayer.hide();

            sbObj.scrollList.scrollTo(0, 0);
            sbObj.shake();

        }, 'json');
    });

};

Shoutbox.prototype = {

    refresh: function() {
        $(this.id + ' button.refresh').attr('disabled', 'disabled');

        var action = this.form.attr('action');
        if ((typeof action) !== 'string') {
            action ='';
        }
        var sbObj = this;
        $.get(action + '?shoutbox_action=update', function(htmlString, textStatus, jqXHR) {
            if (htmlString.length > 0) {
                $(sbObj.id + ' ul.list').html(htmlString);
                sbObj.scrollList.scrollTo(0, 0);
                sbObj.shake();
                $(sbObj.id + ' button.refresh').attr('disabled', null);
            }
        }, 'html');
    }, // END refresh

    shake: function() {
        $list = $(this.id + ' .entries');
        for(var iter = 0;iter < 5;iter++) {
            $list.animate({ left:((iter % 2 == 0 ? 10 : -10)) }, 100);
        }
        $list.animate({left: 0}, 100);
    } // END shake
}

/*----------------------------------------------------------------------------

var Shoutbox = (function() {

    return {

       init: function(sbId) {
            var shoutbox_id = '#' + sbId;
            // add ajax parameter and submit event
 var theForm = $(shoutbox_id+' form');

 var myScroll = new IScroll(shoutbox_id+'_entries');
            console.log(shoutbox_id+'_entries');

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

 ----------------------------------------------------------------------------*/