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
            callServer(['WidgetManager', 'SetContextWidgets'], [
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
                    var full = widget.clone().hide().appendTo(dashboard);

                    // hide columns
                    columns.hide();

                    // make widget fullscreen
                    full.css('width', (dashboard.width() - 20) + 'px').css('float', 'left');

                    // close button
                    $('a.close', full).click(function() {
                        full.hide();
                        columns.show();
                        return false;
                    }).html('Close');

                    // hide other buttons
                    $('a.info, a.minmax, a.settings', full).detach();

                    // normal cursor
                    $(settings.controls, full).css('cursor', 'auto');

                    // load content
                    callServer(['WidgetManager', 'GetWidgetContent'], [
                        widget.attr('id'),
                        'fullscreen',
                        ], function(json) {
                            $('> .content > .scroll', full).html(json);
                        });
                    
                    // display
                    full.show();
                    
                    return false;
                });

            $('form.settings', widget).each(function() {

            // add settings button
            $('<a href="#" class="settings">settings</a>')
                .prependTo(controls)
                .click(function() {
                    $('.settings fieldset', widget).toggle();
                });

            // hide form on init
            $('.settings fieldset').hide();

            // ajax submit
            $('form.settings', widget).submit(function() {
                var settings = {};
                $('input:text', $(this)).each(function() {
                    settings[$(this).attr('name')] = $(this).val();
                });
                callServer(['WidgetManager', 'SaveWidgetSettings'], [
                    widget.attr('id'),
                    settings,
                    ], function(json) {
                    // reload content
                    callServer(['WidgetManager', 'GetWidgetContent'], [
                        widget.attr('id'),
                        widget.closest('.context').attr('id'),
                        ], function(json) {
                            $('> .content > .scroll', widget).html(json);
                        });
                });
                return false;
            });

            }); // /form.settings

            // add info button
            $('<a class="info" href="#" title="' + settings.localizer.info + '">i</a>')
                .prependTo(controls)
                .click(function() {
                    meta.toggle();
                    return false;
                });
            meta.hide().click(function() { $(this).hide(); });

            // add close button
            $('<a class="close" href="#" title="' + settings.localizer.remove + '">x</a>')
                .prependTo(controls)
                .click(function() {
                    callServer(['WidgetManager', 'RemoveWidget'], [
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
            containment: 'document',
            stop: function(event, ui) {
                // reload content
                callServer(['WidgetManager', 'GetWidgetContent'], [
                    ui.item.attr('id'),
                    'default',
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
