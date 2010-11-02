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
        if ($(this).val() == '') {
            $('ul.tree.sortable').sortable('option', 'disabled', false);
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
                    $(this).closest(elemParent).addClass('match');
                }
            });
        });

        // hide non matching
        $('ul.tree > li').not('.match').hide();
        $('ul.tree li.match > ul').show();
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
        .click(function() {
            $(this).hide();
        });

    if (!fixed) {
        flash.delay(3000).fadeOut('slow');
    }

    return flash;
}

/**
 * Call server function
 * @param {array} p_callback
 * @param {object} p_args
 * @param {callback} p_handle
 * @return bool
 */
function callServer(p_callback, p_args, p_handle)
{
    var flash = flashMessage('Processing...', null, true);
    $.ajax({
        'url': g_admin_endpoint,
        'type': 'POST',
        'data': {
            'security_token': g_security_token,
            'callback': p_callback,
            'args': p_args,
        },
        'dataType': 'json',
        'success': function(json) {
            flash.fadeOut();
            if (json.error) {
                flashMessage(json.message, 'error');
                return;
            }

            if (p_handle) {
                p_handle(json);
            }
        },
        'error': function(xhr, textStatus, errorThrown) {
            flashMessage(g_msg_session_expired, 'error', true);
        },
    });
}

/**
 * Call server function
 * @param {array} p_callback
 * @param {object} p_params
 * @param {callback} p_handle
 * @return bool
 */
function callServer(p_callback, p_params, p_handle)
{
    var xhr = $.getJSON(g_admin_endpoint, {
        'callback': p_callback,
        'params': p_params,
        'security_token': g_security_token,
        }, function(data, textStatus, xhr) {
            if (p_handle) {
                p_handle(data.result);
            }
            return true;
        }
    );
    return false;
}

var terms = [];
var autocomplete = false;
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
        if ($(this).val() == '') {
            $('ul.tree.sortable').sortable('option', 'disabled', false);
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
                    $(this).closest(elemParent).addClass('match');
                }
            });
        });

        // hide non matching
        $('ul.tree > li').not('.match').hide();
        $('ul.tree li.match > ul').show();
    });
});
