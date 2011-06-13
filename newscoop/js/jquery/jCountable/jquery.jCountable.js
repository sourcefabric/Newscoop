/*
 * jCountable - jQuery Plugin
 * Simple countable areas for jquery
 *
 * 
 * Author: Nistor Mihai
 * 
 * Version: 1.0 (10/03/2011)
 * Requires: jQuery v1.3+
*/
(function ($) {
    $.fn.extend({
        //plugin name - jCountable
        jCountable: function (options) {
            var defaults = {
                errorLength: 140,
                warnLength: '80%',
                message: {
                    value: '',
                    container: false,
                    hasParent: {
                        width: true,
                        height: false
                    },
                    showMaximum: true,
                    separator: '/',
                    className: 'j-countable',
                    classNames: {
                        message: 'message',
                        warn: 'warn',
                        error: 'error'
                    },
                    blur: ['fadeTo', ['fast', 0]],
                    focus: ['fadeTo', ['fast', 1]]
                },
                overflow: false
            };
            var options = $.extend(true, defaults, options);
            // allow jQuery chaining
            return this.each(function () {
                var opt = options.message;
                var obj = $(this);
                // if a maxlength attribute is set then this has priority no.1
                var errorLength = options.errorLength;
                if ((obj.attr('maxlength')>0) && (obj.attr('maxlength')<524288) ) errorLength = parseInt(obj.attr('maxlength'));
                // calculate warnLength by procentual if a procent % sign is found
                var warnLength = (options.warnLength + '').indexOf('%') + 1 ? parseInt(options.warnLength) * errorLength / 100 : options.warnLength;
                warnLength = parseInt(warnLength);

                var messageContainer = opt.container;
                if (!messageContainer)  {
                	messageContainer = $('<div class="' + opt.className + '"></div>');
                	obj.after(messageContainer);
                }
                if (opt.hasParent.width) messageContainer.width(obj.width());
                if (opt.hasParent.height) messageContainer.height(obj.height());
                isTextArea = obj.get(0).tagName.toUpperCase() == 'TEXTAREA';

                function count(event) {
                    var length = obj.val().length;
                    var
                    isOverflow = (length > errorLength + 1),
                        isError = (length >= errorLength),
                        isWarn = (length >= warnLength);
                    //scroll to the last position
                    if (isOverflow && !options.overflow && isTextArea) {
                        var lastTop = obj.scrollTop();
                        obj.val(obj.val().substring(0, errorLength));
                        obj.scrollTop(lastTop);
                    }
                    //toggle warn class is warn but not error
                    messageContainer.toggleClass(opt.classNames.warn, isWarn && !isError);
                    //toggle error class is error
                    messageContainer.toggleClass(opt.classNames.error, isError);
                    messageContainer.html(opt.value + (opt.showMaximum ? length : errorLength - length) + (opt.showMaximum ? opt.separator + errorLength : ''));
                }
                obj.bind('keydown keyup keypress', count).bind('focus paste', function () {
                    messageContainer[opt.focus[0]].apply(messageContainer, opt.focus[1]);
                    setTimeout(count, 1);
                }).bind('blur', function () {
                    messageContainer[opt.blur[0]].apply(messageContainer, opt.blur[1]);
                    return false;
                });
            });
        }
    });
})(jQuery);