(function ( $ ) {

/**
 * Widgets plugin for JQuery
 */
$.fn.widgets = function (options) {
    var contexts = this;
    var settings = {
        url: '',
        security_token: '',
        default_context: 'preview',
        widgets: '> .widget',
        controls: '> .header',
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
            $('<a class="minmax" href="#" title="Fullscreen">Fullscreen</a>')
                .prependTo(controls)
                .click(function() {
                    var dashboard = widget.closest('#dashboard');
                    var columns = $('.column', dashboard);
                    var full = widget.clone().hide().appendTo(dashboard);

                    // hide columns
                    columns.hide();

                    // make widget fullscreen
                    full.css('width', (dashboard.width()) + 'px').css('float', 'left');

                    // close button
                    $('a.close', full).click(function() {
                        full.hide();
                        columns.show();
                        return false;
                    }).html('Close');

                    // hide other buttons
                    $('a.info, a.minmax', full).detach();

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

            // add info button
            $('<a class="info" href="#" title="Info">i</a>')
                .prependTo(controls)
                .click(function() {
                    meta.toggle();
                    return false;
                });
            meta.hide().click(function() { $(this).hide(); });

            // add close button
            $('<a class="close" href="#" title="Remove">x</a>')
                .prependTo(controls)
                .click(function() {
                    callServer(['WidgetManager', 'RemoveWidget'], [
                        widget.attr('id'),
                        ], function(json) {
                            widget.hide(500, function() {
                                $(this).detach();
                                updateOrder();
                            })
                        });
                    return false;
                });
        });

        // make sortable
        $(this).sortable({
            connectWith: contexts,
            placeholder: 'widget-placeholder',
            handle: '.header',
            forcePlaceholderSize: true,
            delay: 100,
            opacity: 0.8,
            containment: 'document',
            stop: function(event, ui) {
                updateOrder();
                getContent(ui.item);
            },
        }).css({
            minHeight: '40px',
        });
    });
};

})( jQuery );
