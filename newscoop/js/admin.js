var terms = [];

$(function() {
	$('.flash ui-state-error').click(function(){ $(this).hide(); })
    // main menu
    $('.main-menu-bar ul.navigation > li > a').each(function(i) {
        var menu = $(this);

        if (i > 0 && !(menu.parent().data('menu') == 'not-menu')) {
            $('<span />').addClass('fg-button-ui-icon fg-button-ui-icon-triangle-1-s').prependTo(menu);

            menu.topmenu({
                content: '<ul>' + menu.next('ul').html() + '</ul>',
                flyOut: true,
                showSpeed: 150
            });
        }

        // add css/attributes to main links
        menu.attr('tabindex', i)
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
            minLength: 3,
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

                if (match.length == 0) {
                    $('form#topicsForm').keypress(function(event) { return event.keyCode != 13; });
                } else {
                    $('form#topicsForm').unbind("keypress");
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
        var value = $(this).val();
        $('ul.tree > li').each(function() {
            var li = $(this);
            $(elem, li).each(function() {
                if ($(this).text().search(re) >= 0) {
                    li.addClass('match');
                    $(this).addClass('match');
                    $(this).parentsUntil('ul.tree').addClass('match');
                    // only check if topic text matches value exactly
                    if ($(this).text() == value) {
                        $('#' + $(this).attr('for')).attr('checked', 'checked');
                    }
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
    
    //show all topics 
    $('#show_all_topics').click(function(){
    	$('input[name=search].topics').val('');
    	$('input[name=search].topics').change();
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
    $('.content table, .sidebar table').each(function() {
        $('tr:odd', $(this)).addClass('odd');
    });

    // confirmations
    $('.confirm[title]').click(function() {
        var title = $(this).attr('title');

        // first letter to lowercase
        title = title.charAt(0).toLowerCase() + title.slice(1);

        return confirm(localizer.confirm + ' ' + title + '?');
    });

    $('a.delete.icon').each(function() {
        $(this).html('');
        $('<span />')
            .addClass('ui-icon ui-icon-closethick')
            .prependTo($(this));
    });

    // zend_form utils
    $('dl.zend_form').each(function() {
        var emptyRegExp = /\S/;
        var form = $(this);
        // hide hidden fields
        $('input:hidden', form).each(function() {
            if ($(this).next('input:checkbox').size()) {
        		return;
            }

            if ($(this).attr('name') == 'MAX_FILE_SIZE') {
                return;
            }

            var dd = $(this).closest('dd');
            var dt = dd.prev('dt');
            var errors = $('ul.errors', dd);

            if (emptyRegExp.test(dt.html())) { // remove if empty
                dt.hide().detach().appendTo(form);
            }

            if (errors.length > 0) { // keep dd for errors
                return;
            }

            if ($('*', dd).size() == $('input:hidden', dd).size()) { // if contains only hiddens
                dd.hide().detach().appendTo(form);
            }
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
            $(this).remove();
        });

    if (!fixed) {
        flash.delay(3000).fadeOut('slow',function(){$(this).remove()});
    }

    return flash;
}

var queue = [];
$(document.body).data('loginDialog',false)
/**
 * Call server function
 * @param {array} p_callback
 * @param {object} p_args
 * @param {callback} p_handle
 * @return bool
 */
function callServer(p_callback, p_args, p_handle, p_direct, p_custom_url)
{
    if (!p_args) {
        p_args = [];
    }
	if (undefined === p_direct) {
		p_direct = false;
	}
    if (undefined === p_custom_url) {
        p_custom_url = false;
    }

    var use_method = 'POST';

	var use_url = (p_direct) ? (p_callback) : (g_admin_url + '/json.php');

    if (p_custom_url) {
        use_url = p_custom_url;
    }

    if(typeof(use_url) == 'object') {
        if (typeof use_url['method'] != 'undefined') {
            use_method = use_url['method'];
        }
        use_url = use_url['url'];
    }

	var default_data = {
            'callback': p_callback,
            'args': p_args
	}
	var use_data = (p_direct) ? p_args : default_data;
    use_data['security_token'] = g_security_token;

    var flash = flashMessage(localizer.processing, null, true);
    $.ajax({
        'url': use_url,
        'type': use_method,
		'data': use_data,
        'dataType': 'json',
        'success': function(json) {
			window.ajax_had_problems = false;
            flash.fadeOut();

            if (json != undefined && json.error_code != undefined) {
                flashMessage(json.error_message, 'error', true);
                return;
            }

            if (p_handle) {
                p_handle(json);
            }
        },
        'error': function(xhr, textStatus, errorThrown) {
			window.ajax_had_problems = true;
        	if(xhr.status == '401')
        	{
        		flash.hide();
        		if (!$(document.body).data('loginDialog')) {
        			loginIframe = $('<iframe />')
        				.attr( 'src', g_admin_url+'/login?ajax=true' )
        				.attr( 'frameborder', 0 )
        				.attr( 'width', 500 )
        				.attr( 'height', 400 )
        				.css({ width: 500, height: 400, padding: 0 });
        			$(document.body).data( 
        				'loginDialog',
        				loginIframe.dialog
        				({ 
        					title: localizer.session_expired + ' ' + localizer.please + ' ' + localizer.login,
        					width: 500, 
        					height: 400, 
        					modal: true,
        					resizable: false,
        					open: function(evt, ui) {
        						$(this).width(500);
        						var parentDiv = $(this).parents('.ui-dialog').eq(0)
        						parentDiv.css('z-index', parseInt( parentDiv.siblings('.ui-widget-overlay').css('z-index'))+1)
        					},
        					close: function(evt, ui) {
        						$(document.body).removeData('loginDialog')
        					}
        				})
        			);
        		}

                popupFlash = flashMessage(localizer.session_expired + ' ' + localizer.please + ' <a href="'+g_admin_url + '/login.php" target="_blank">' + localizer.login + '</a>.', 'error', false);
        	} else {
        		popupFlash = flashMessage(localizer.connection_interrupted + '! ' + localizer.please + ' ' + localizer.try_again_later + '!', 'highlight', false);
        	}
            
            // store request
            queue.push({
                callback: p_callback,
                args: p_args,
                handle: p_handle,
				direct: p_direct
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
        callServer(request.callback, request.args, request.handle, request.direct);
    }
}
