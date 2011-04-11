var terms = [];
$(document).ready(function() {
    // topics search autocomplete
    $('input[name=search].topics').each(function() {
        var input = $(this);
        input.autocomplete({
            source: function(request, response) {
                if (terms.length == 0) { // populate terms
                    $('ul.tree.sortable strong').each(function() {
                        terms.push($(this).text());
                    });
                }
                if (terms.length == 0) { // still needs to populate
                    $('ul.tree label').each(function() {
                        terms.push($(this).text());
                    });
                }

                var match = [];
                var re = new RegExp(request.term, "i");
                for (i = 0; i < terms.length; i++) {
                    if (terms[i].search(re) >= 0) {
                        match.push(terms[i]);
                    }
                }
                response(match);
            },
            close: function(event, ui) {
                input.change(); // trigger search
            }
        });
    }).change(function() {
        // reset
        $('ul.tree *').removeClass('match');
        $('ul.tree li, ul.tree ul').show();
        $('ul.tree.sortable').sortable('option', 'disabled', true);
        $('span.open', $('ul.tree')).each(function() {
            $(this).removeClass('opened');
            if ($('ul', $(this).closest('li')).size()) {
                $(this).addClass('closed');
            }
        });
        $('> a', 'ul.tree li').text('+');

        if ($(this).val() == '') {
            $('ul.tree.sortable').sortable('option', 'disabled', false);
            $('ul.tree ul').hide();
            return;
        }

        // search targets
        var elem = 'label';
        var elemParent = 'li';
        if ($('ul.tree').hasClass('sortable')) {
            elem = 'strong';
            elemParent = '.item';
        }

        // search
        var re = new RegExp($(this).val(), "i");
        $('ul.tree > li').each(function() {
            var li = $(this);
            $(elem, li).each(function() {
                if ($(this).text().search(re) >= 0) {
                    li.addClass('match');
                    $(this).addClass('match');
                    $(this).parentsUntil('ul.tree').addClass('match');
                }
            });
        });

        // hide non matching
        $('ul.tree > li').not('.match').hide();
        $('ul.tree li.match > ul').show();
        $('span.open', $('ul.tree li.match')).each(function() {
            $(this).removeClass('closed');
            if ($('ul', $(this).closest('li')).size()) {
                $(this).addClass('opened');
            }
        });
        $('> a', 'ul.tree li.match').text('-');
    });

    // set date pickers
    $('input.date, input.datetime').each(function() {
        // common settings
        var settings = {
            dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm:ss',
            showSeconds: true,
            showOn: 'both',
            buttonImage: g_admin_img + '/calendar_big.png',
            buttonImageOnly: true
        };

        // update settings by classes
        var classes = $(this).attr('class').split(' ');
        for (var i = classes.length; i > 0; i--) {
            var class_ary = classes[i-1].split('_');
            if (class_ary.length == 2) {
                settings[class_ary[0]] = class_ary[1];
            }
        }

        if ($(this).hasClass('date')) {
            $(this).datepicker(settings);
        } else {
            $(this).datetimepicker(settings);
        }
    });

    // display flash messages
    try {
        if (user_msgs != '') {
            flashMessage(user_msgs);
        }
    } catch (e) {};

    // rise limit for google gadget setting
    $('input#googlegadget-code').each(function() {
            $(this).attr('maxlength', '500');
        });
});

/**
 * Displays flash message.
 *
 * @param string message
 * @param string type
 * @return object
 */
function flashMessage(message, type, fixed)
{
    if (type) {
        messageClass = type;
    } else { // default is info
        messageClass = 'highlight';
    }

    // replace + to spaces
    message = message.replace(/\+/g, " ");

    var flash = $('<div class="flash ui-state-' + messageClass + '">' + message + '</div>')
        .appendTo('body')
        .css('z-index', '10000')
        .css('position', 'fixed')
        .css('top', '13px')
        .css('left', '33%')
        .css('width', '33%')
        .css('padding', '8px')
        .css('font-size', '1.3em')
        .click(function() {
            $(this).hide();
        });

    if (!fixed) {
        flash.delay(3000).fadeOut('slow');
    }

    return flash;
}

var queue = [];

var popupFlash = null;

/**
 * Ask user to relogin for a server function calling, e.g. after session expired or wrong security token (after a different relogin)
 * @param {array} p_callback
 * @param {object} p_args
 * @param {callback} p_handle
 * @return none
 */
function reloginRequest(p_callback, p_args, p_handle)
{
    try {
        var login = window.open(g_admin_url + '/login.php?request=ajax&f_force_login=1', 'login', 'height=500,width=500');
        login.focus();
    } catch (e) {}
    // make the flash if not already done (e.g. at the article editing page where several ajax calls are done)
    if (!popupFlash) {
        popupFlash = flashMessage(localizer.session_expired + ' ' + localizer.please + ' <a href="'+g_admin_url + '/login.php?f_force_login=1" target="_blank">' + localizer.login + '</a>.', 'error', true);
    }

    // store request
    queue.push({
        callback: p_callback,
        args: p_args,
        handle: p_handle
    });
};

/**
 * Call server function
 * @param {array} p_callback
 * @param {object} p_args
 * @param {callback} p_handle
 * @return bool
 */
function callServer(p_callback, p_args, p_handle)
{
    if (!p_args) {
        p_args = [];
    }

    var flash = flashMessage(localizer.processing, null, true);
    $.ajax({
        'url': g_admin_url + '/json.php',
        'type': 'POST',
        'data': {
            'security_token': g_security_token,
            'callback': p_callback,
            'args': p_args
        },
        'dataType': 'json',
        'success': function(json) {
            /* note that the value of the 'sec_token_err' variable is defined at classes ServerRequest.php file too, as ServerRequest::ERROR_SECURITY_TOKEN */
            var sec_token_err = 2;

            flash.fadeOut();

            if (json.error_code) {
                if (sec_token_err == json.error_code) {
                    // making relogin available even when at a session with wrong security token (happens when user relogged at a different window)
                    reloginRequest(p_callback, p_args, p_handle);
                } else {
                    flashMessage(json.error_message, 'error', true);
                }
                return;
            }

            if (p_handle) {
                p_handle(json);
            }
        },
        'error': function(xhr, textStatus, errorThrown) {
            // standard relogin situation, i.e. after session expired
            flash.hide();
            reloginRequest(p_callback, p_args, p_handle);
        }
    });
}

/**
 * Set security token
 * @param string security_token
 * @return void
 */
function setSecurityToken(security_token)
{
    g_security_token = security_token;
    $('input[name=security_token]').val(security_token);

    if (popupFlash) {
        popupFlash.hide();
        popupFlash = null;
    }

    // restore request
    for (var i = 0; i < queue.length; i++) {
        var request = queue[i];
        callServer(request.callback, request.args, request.handle);
    }
}
