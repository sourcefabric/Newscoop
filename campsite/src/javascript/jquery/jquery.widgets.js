(function ( $ ) {

$.fn.widgets = function (options) {
    var areas = this;
    var settings = {
        'url': '',
        'security_token': '',
    };

    return this.each(function() {
        if (options) {
            $.extend(settings, options)
        }

        // set up items
        $(this).find('> *').each(function() {
            $(this).css({
                cursor: 'move',
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
                areas.each(function() {
                    var area = $(this).attr('id');
                    $.getJSON(settings.url, {
                        'callback': ['Extension_Area', 'SaveWidgets'],
                        'params': {
                            'area': area,
                            'widgets': $(this).sortable('toArray'),
                        },
                        'security_token': settings.security_token,
                        }, function(response) {
                            if (!response.status) {
                                alert(response.message);
                            } else { // reload widgets content
                                $('.widget', area).each(function() {
                                    var widget = $(this);
                                    $.getJSON(settings.url, {
                                        'callback': ['Extension_Widget', 'GetContent'],
                                        'params': {
                                            'area': widget.closest('.area').attr('id'),
                                            'widget': widget.attr('id'),
                                            },
                                        'security_token': settings.security_token,
                                        }, function(response) {
                                            if (!response.status) {
                                                alert(response.message);
                                            } else {
                                                $('> .widget-content', widget).html(response.result);
                                            }
                                        });
                                });
                            }
                        });
                });
            },
        }).css({
            minHeight: '40px',
        });
    });
};

})( jQuery );
