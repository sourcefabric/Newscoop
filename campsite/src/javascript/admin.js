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
            },
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

    var flash = $('<div class="flash ui-state-' + messageClass + '"><p>' + message + '</p></div>')
        .appendTo('body')
        .css('z-index', '10000')
        .click(function() {
            $(this).hide();
        });

    if (!fixed) {
        flash.delay(3000).fadeOut('slow');
    }

    return flash;
}

var queue = [];

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

    var flash = flashMessage('Processing...', null, true);
    $.ajax({
        'url': g_admin_url + '/json.php',
        'type': 'POST',
        'data': {
            'security_token': g_security_token,
            'callback': p_callback,
            'args': p_args,
        },
        'dataType': 'json',
        'success': function(json) {
            flash.fadeOut();

            if (json.error_code) {
                flashMessage(json.error_message, 'error', true);
                return;
            }

            if (p_handle) {
                p_handle(json);
            }
        },
        'error': function(xhr, textStatus, errorThrown) {
            flash.hide();
            var login = window.open(g_admin_url + '/login.php?request=ajax', 'login', 'height=400,width=500');
            login.focus();
            popupFlash = flashMessage('Session expired. Please <a href="'+g_admin_url + '/login.php" target="_blank">re-login</a>.', 'error', true);

            // store request
            queue.push({
                callback: p_callback,
                args: p_args,
                handle: p_handle,
            });
        },
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
    }

    // restore request
    for (var i = 0; i < queue.length; i++) {
        var request = queue[i];
        callServer(request.callback, request.args, request.handle);
    }
}
