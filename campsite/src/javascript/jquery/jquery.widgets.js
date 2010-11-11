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
        controls_fadein: 300,
        controls_fadeout: 800,
        controls_delay: 2100,
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
     * Get content for widget.
     * @param object widget
     */
    var getContent = function(widget)
    {
        var context = widget.closest('.context').attr('id');
        callServer(['WidgetManager', 'GetWidgetContent'], [
            widget.attr('id'),
            context,
            ], function(result) {
                $('> .content', widget).html(result).show();
            }
        );
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

            // ui interaction
            widget.hover(function() {
                controls.clearQueue();
                controls.fadeTo(settings.controls_fadein, 1);
            }, function () {
                controls.delay(settings.controls_delay)
                    .fadeTo(settings.controls_fadeout, 0);
            });

            // init controls
            controls.delay(settings.controls_delay).fadeTo(settings.controls_fadeout, 0);
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
