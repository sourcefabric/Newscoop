(function ( $ ) {

$.fn.widgets = function (options) {
    var areas = this;
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
        areas.each(function() {
            var area = $(this).attr('id');
            callServer(['Extension_Area', 'SaveWidgets'], {
                'area': area,
                'widgets': $(this).sortable('toArray'),
                });
        });

    };

    /**
     * Get content for widget.
     * @param object widget
     */
    var getContent = function(widget)
    {
        callServer(['Extension_Widget', 'GetContent'], {
            'area': widget.closest('.area').attr('id'),
            'widget': widget.attr('id'),
            }, function(result) {
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
                        $(this).detach().appendTo($('.area').last()).show('slow');
                        updateOrder();
                        getContent($(this));
                    });
                });
        });

        // make sortable
        $(this).sortable({
            connectWith: areas,
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
