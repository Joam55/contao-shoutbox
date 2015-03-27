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
                sbObj.scrollList.scrollTo(0, 0);
                sbObj.shake();
            }
            else {
                alert(jsonObj.message);
            }

            $('button', $form).attr('disabled', null);
            sbObj.blockLayer.hide();

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
        for(var iter = 0;iter < 3;iter++) {
            $list.animate({ top:((iter % 2 == 0 ? 10 : -10)) }, 50);
        }
        $list.animate({top: 0}, 50);
    } // END shake
}

