(function ( $ ) {

$.fn.widgets = function (options) {
    var contexts = this;
    var settings = {
        'url': '',
        'security_token': '',
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
        callServer(['WidgetManager', 'GetWidgetContent'], [
            widget.attr('id'),
            widget.closest('.context').attr('id'),
            ], function(result) {
                $('> .content', widget).html(result);
            }
        );
    };

    return this.each(function() {
        if (options) {
            $.extend(settings, options)
        }

        // set up items
        $(this).find('> *').each(function() {
            var widget = $(this);

            // set widget
            widget.css({
                cursor: 'move',
            });

            // add close button
            $('<a class="close" href="#"><span></span>X</a>')
                .appendTo($('.header', widget))
                .click(function() {
                    widget.hide('slow', function() {
                        $(this).detach().appendTo($('.context').last()).show('slow');
                        updateOrder();
                        getContent($(this));
                    });
                });
        });

        // make sortable
        $(this).sortable({
            connectWith: contexts,
            placeholder: 'widget-placeholder',
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
