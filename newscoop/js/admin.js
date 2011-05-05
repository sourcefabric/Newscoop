var terms = [];
$(function() {
    // main menu
    $('.main-menu-bar ul.navigation ul').hide();
    $('.main-menu-bar ul.navigation > li > a').each(function(i) {
        var menu = $(this);

        // init menu for all but first
        if (i > 0) {
            menu.topmenu({
                content: '<ul>' + menu.next('ul').html() + '</ul>',
                flyOut: true,
                showSpeed: 150
            });

            $('<span />').addClass('fg-button-ui-icon fg-button-ui-icon-triangle-1-s') .prependTo(menu);
        }

        // add css/attributes to main links
        menu.attr('tabindex', i)
            .addClass('fg-button ui-widget fg-button-icon-right fg-button-ui-state-default fg-button-ui-corner-all')
            .hover(function() {
                $(this).removeClass('fg-button-ui-state-default')
                    .addClass('fg-button-ui-state-focus');
            }, function() {
                $(this).removeClass('fg-button-ui-state-focus')
                    .addClass('fg-button-ui-state-default');
            });
    });

    // sticky buttons
    var sticky_limit = 0;
    $(window).scroll(function() {
        if ($('.sticky').size() == 0) {
            return false; // no sticky
        }

        var windowTop = $(window).scrollTop();
        var stickyTop = $('.sticky').offset().top;
        if (windowTop > stickyTop && sticky_limit == 0) {
            $('.sticky').css('width', '100%').css('position', 'fixed').css('top', '0');
            sticky_limit = stickyTop;
        }
        if (sticky_limit > 0 && windowTop < sticky_limit) {
            $('.sticky').css('position', 'relative');
            sticky_limit = 0;
        }
    });

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

    // zebra
    $('.content table tr:odd, .sidebar table tr:odd').addClass('odd');

    // confirmations
    $('.confirm[title]').click(function() {
        var title = $(this).attr('title');

        // first letter to lowercase
        title = title.charAt(0).toLowerCase() + title.slice(1);

        return confirm(localizer.confirm + ' ' + title + '?');
    });

    // add plus icons
    $('a.add').each(function() {
        $(this).addClass('ui-icon-wrapper');
        $('<span />').addClass('ui-icon ui-icon-plus')
            .prependTo($(this));
    });

    // add cross icon for delete
    $('a.delete.icon').each(function() {
        $(this).html('');
        $('<span />')
            .addClass('ui-icon ui-icon-closethick')
            .prependTo($(this));
    });

    // zend_form utils
    $('dl.zend_form').each(function() {
        var form = $(this);

        // hide hidden fields
        $('input:hidden', form).each(function() {
            var dd = $(this).closest('dd');
            var dt = dd.prev('dt');
            var errors = $('ul.errors', dd);

            dt.hide().detach().appendTo(form);

            if (errors.length > 0) { // keep dd for errors
                return;
            }

            dd.hide().detach().appendTo(form);
        });

        // hide fieldsets dt
        $('fieldset', form).each(function() {
            $(this).closest('dd').prev('dt').hide();
        });

        // hide submit dt
        $('input:submit', form).each(function() {
            $(this).closest('dd').addClass('buttons').prev('dt').hide();
        });

        // toogle fieldsets
        $('fieldset.toggle legend', form).click(function() {
            $(this).closest('fieldset').toggleClass('closed');
            $('+ dl', $(this)).toggle();
        }).each(function() {
            $(this).css('cursor', 'pointer');
            $('<span />').addClass('ui-icon ui-icon-triangle-2-n-s').prependTo($(this));

            // toggle on load if not contain errors
            if (!$('ul.errors', $(this).closest('fieldset')).size()) {
                $(this).click();
            }
        });

        // acl rule type colours switch
        $('input:radio.acl.type', form).change(function() {
            if ($(this).attr('checked')) {
                $(this).closest('label').addClass('checked').siblings('label').removeClass('checked');
            }
        }).change();
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
            popupFlash = flashMessage(localizer.session_expired + ' ' + localizer.please + ' <a href="'+g_admin_url + '/login.php" target="_blank">' + localizer.login + '</a>.', 'error', true);

            // store request
            queue.push({
                callback: p_callback,
                args: p_args,
                handle: p_handle
            });
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
    }

    // restore request
    for (var i = 0; i < queue.length; i++) {
        var request = queue[i];
        callServer(request.callback, request.args, request.handle);
    }
}
