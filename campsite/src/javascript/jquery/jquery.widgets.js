(function ( $ ) {

/**
 * Widgets plugin for JQuery
 */
$.fn.widgets = function (options) {
    var contexts = this;
    var settings = {
        widgets: '> .widget',
        controls: '> .header',
        localizer: {},
    };

    /**
     * Update widgets order.
     * @return void
     */
    var updateOrder = function()
    {
        contexts.each(function() {
            var context = $(this).attr('id');
            callServer(['WidgetContext', 'setWidgets'], [
                context,
                $(this).sortable('toArray'),
            ]);
        });

    };

    /**
     * Widgets plugin init
     * @return JQuery
     */
    return this.each(function() {
        if (options) {
            $.extend(settings, options)
        }

        // set up items
        $(this).find(settings.widgets).each(function() {
            var widget = $(this);
            var controls = $(settings.controls, widget);
            var meta = $('dl.meta', widget);

            // add min/max button
            $('<a class="minmax" href="#">full</a>')
                .prependTo(controls)
                .click(function() {
                    var dashboard = widget.closest('#dashboard');
                    var columns = $('.column', dashboard);
                    var full = widget.clone()
                        .hide()
                        .appendTo(dashboard)
                        .css('list-style-type', 'none')
                        .css('width', (dashboard.width() - 14) + 'px')
                        .css('float', 'left')
                        .css('margin-top', '13px');

                    // hide columns
                    columns.hide();

                    // close button
                    $('a.close', full).click(function() {
                        columns.show();
                        full.detach();
                        return false;
                    }).html('Close');

                    // hide other buttons
                    $('a.info, a.minmax, a.settings', full).detach();

                    // normal cursor
                    $(settings.controls, full).css('cursor', 'auto');

                    // load content
                    full.show();
                    $('> .content .scroll', full).html('<p>Loading..</p>');
                    callServer(['WidgetRendererDecorator', 'render'], [
                        widget.attr('id'),
                        'fullscreen',
                        true,
                        ], function(json) {
                            $('> .content > .scroll', full).html(json);
                        });
                    
                    return false;
                });

            $('form.settings', widget).each(function() {

            // add settings button
            $('<a href="#" class="settings">settings</a>')
                .prependTo(controls)
                .click(function() {
                    $('.settings fieldset', widget).toggle();
                    $('.scroll', widget).css('min-height', $('.extra', widget).height() + 'px');
                    return false;
                });

            // hide form on init
            $('.settings fieldset').hide();

            // ajax submit
            $('form.settings', widget).submit(function() {
                var fieldset = $('fieldset', $(this));
                var settings = {};
                $('input:text', $(this)).each(function() {
                    settings[$(this).attr('name')] = $(this).val();
                });
                callServer(['WidgetManagerDecorator', 'update'], [
                    widget.attr('id'),
                    {'settings': settings},
                    ], function(json) {
                    // reload content
                    callServer(['WidgetRendererDecorator', 'render'], [
                        widget.attr('id'),
                        widget.closest('.context').attr('id'),
                        true,
                        ], function(json) {
                            $('> .content > .scroll', widget).html(json);
                            fieldset.fadeOut();
                    });

                    // reload title if needed
                    if (settings['title']) {
                        callServer(['WidgetManagerDecorator', 'getSetting'], [
                            widget.attr('id'),
                            'title',
                            ], function(json) {
                                $('> .header h3', widget).text(json);
                        });
                    }
                });
                return false;
            });

            }); // /form.settings

            // add info button
            $('<a class="info" href="#" title="' + settings.localizer.info + '">i</a>')
                .prependTo(controls)
                .click(function() {
                    meta.toggle();
                    $('.scroll', widget).css('min-height', $('.extra', widget).height() + 'px');
                    return false;
                });
            meta.hide().click(function() {
                $(this).hide();
                $('.scroll', widget).css('min-height', $('.extra', widget).height() + 'px');
            });

            // add close button
            $('<a class="close" href="#" title="' + settings.localizer.remove + '">x</a>')
                .prependTo(controls)
                .click(function() {
                    callServer(['WidgetManagerDecorator', 'delete'], [
                        widget.attr('id'),
                        ], function(json) {
                            widget.hide(500, function() {
                                $(this).detach();
                            })
                        });
                    return false;
                });

            // add move cursor
            controls.css('cursor', 'move');
        });

        // make sortable
        $(this).sortable({
            connectWith: contexts,
            placeholder: 'widget-placeholder',
            handle: '.header',
            forcePlaceholderSize: true,
            opacity: 0.8,
            stop: function(event, ui) {
                // reload content
                callServer(['WidgetRendererDecorator', 'render'], [
                    ui.item.attr('id'),
                    'default',
                    true,
                    ], function(json) {
                        $('> .content > .scroll', ui.item).html(json);
                    });
                updateOrder();
            },
        }).css({
            minHeight: '40px',
        });
    });
};

})( jQuery );
