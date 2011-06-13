function setEvents() {
    $('.fg-button').live('mouseenter', function(){
        $(this).removeClass('ui-state-default').addClass('ui-state-focus');
    });
    $('.fg-button').live('mouseleave', function(){
        $(this).removeClass('ui-state-focus').addClass('ui-state-default');
    });

    $('.icon-button, .text-button').live('mouseenter', function(){
        $(this).addClass('ui-state-hover');
    });
    $('.icon-button, .text-button').live('mouseleave', function(){
        $(this).removeClass('ui-state-hover');
    });

    // upload form actions
    $('#upload-form').submit(function(){
        flashMessage(localizer.uploading, '', true);
        $('#upload-submit').attr('disabled', true);
        $('#attach-submit').attr('disabled', true);
    });

    $('#attach-submit').click(function(){
        $('#upload-action').val('attach');
        $('#upload-form').submit();
    });

    $('#upload-sharing').live('change', function(){
        $('#div-public-sharing').toggleClass('selected');
        $('#div-private-sharing').toggleClass('selected');
    });

    $('#upload-link-more-options').click(function() {
        $('#upload-div-more-options').toggle('fast');
        $(this).toggleClass('opened');
        return false;
    }).next().hide();
    // end upload form actions

    // track list actions
    $('.track-play').live('click', function(){
        $('#player-' + this.id).toggle();
        return false;
    });

    $('.track-edit').live('click', function(){
        flashMessage(localizer.processing, '', true);
        var attachement = $('#attachement').val();
        var article = $('#article').val();
        $.get('controller.php', {
            action:'edit',
            track:this.id,
            attachement:attachement,
            article:article
        },
        function(response){
            processResponse(response, '#edit-form');
            if (response.html) {
                $('#edit-div-more-options').hide();
                $('#edit-tab').show();
                $('.tabs').tabs('select', '#tabs-3');
                setDateFields();
            }
        });
        return false;
    });

    $('.track-attach').live('click', function(){
        flashMessage(localizer.processing, '', true);
        $(this).fadeOut('fast');
        $.get('controller.php', {
                action:'attach',
                article:$('#article').val(),
                track:this.id
            },
            function(response){
                processResponse(response, '#track-list');
                if (response.ok) {
                    parent.$.fancybox.reload = true;
                }
        });
        return false;
    });

    $('.addtoset').live('click', function(){
        flashMessage(localizer.processing, '', true);
        var button = this;
        $.get('controller.php', {
                action:'addtoset',
                track:$('#set-track').val(),
                set:this.id
            },
            function(response){
                processResponse(response, '');
                if (response.ok) {
                    $(button).hide();
                    $('#' + button.id + ' + .removefromset').fadeIn('fast');
                }
        });
        return false;
    });

    $('.removefromset').live('click', function(){
        flashMessage(localizer.processing, '', true);
        var button = this;
        $.get('controller.php', {
                action:'removefromset',
                track:$('#set-track').val(),
                set:this.id
            },
            function(response){
                processResponse(response, '');
                if (response.ok) {
                    $(button).hide();
                    $('#' + button.id).fadeIn('fast');
                }
        });
        return false;
    });

    $('.track-set').live('click', function(){
        flashMessage(localizer.processing, '', true);
        $.get('controller.php', {
                action:'setlist',
                track:$(this).attr('id'),
            },
            function(response){
                if (response.ok) {
                    var buttons = {};
                    buttons[localizer.cancel] = function(){$(this).dialog('close')};
                    $('#dialog').detach();
                    $('<div id="dialog" title="'+localizer.setlist+'"></div>')
                    .appendTo('body')
                    .dialog({
                        resizable: true,
                        modal: true,
                        width:400,
                        height:500,
                        maxHeight:500,
                    });
                    processResponse(response, '#dialog');
                } else {
                    processResponse(response, '');
                }
        });
        return false;
    });

    $('.track-delete').live('click', function(){
        var link = this;
        var title = $('#title-' + this.id).html();
        okCancel(localizer.attention, localizer.deleteQuestion + '<br />"' + title + '"', function(){
            flashMessage(localizer.processing, '', true);
            $('#paging-action').val('reload');
            var search = $('#search-form').serializeArray();
            $(link).fadeOut('fast');
            $.post('controller.php', {action:'delete', track:link.id, search:search}, function(response){
                processResponse(response, '#track-list');
            });
        });
        return false;
    });
    // end track list actions

    // edit form actions
    $('#edit-form').submit(function(){
        flashMessage(localizer.processing, '', true);
        $('#edit-submit').attr('disabled', true);
        var $theframe = $('<iframe name="edit_iframe" id="edit_iframe" width="0" height="0" frameborder="0" style="border: none; display: none; visibility: hidden;"></iframe>');
        $(this).append($theframe).attr('target', 'edit_iframe');
        $('#edit_iframe').load(function(){
            var response = $('#edit_iframe').contents().find('body').html();
            $('#edit_iframe').unbind('load');
            eval('response='+ response);
            if (response.ok) {
                var message = response;
                $('#paging-action').val('reload');
                var search = $('#search-form').serializeArray();
                $.post('controller.php', {action:'search', search:search}, function(response){
                    processResponse(response, '#track-list');
                    $('#edit-tab').hide();
                    $('.tabs').tabs('select', '#tabs-2');
                    $('#paging-action').val('');
                    processResponse(message, '#track-list');
                });
            } else {
                processResponse(response, '#edit-form');
                $('#edit-submit').attr('disabled', false);
            }
        });
        return true;
    });

    $('#edit-cancel').live('click', function(){
        $('#edit-tab').hide();
        $('.tabs').tabs('select', '#tabs-2');
        return false;
    });

    $('#edit-attach').live('click', function(){
        $('#edit-attach').val('1');
        $('#edit-form').submit();
        parent.$.fancybox.reload = true;
        return false;
    });

    $('#edit-sharing').live('change', function(){
        $('#div-edit-public-sharing').toggleClass('selected');
        $('#div-edit-private-sharing').toggleClass('selected');
    });

    $('#edit-link-more-options').live('click', function() {
        $('#edit-div-more-options').toggle('fast');
        $(this).toggleClass('opened');
        return false;
    }).next().hide();

    $('#edit-play').live('click', function(){
        $('#edit-player').toggle();
        return false;
    });
// end edit form actions

    // search form actions
    $('#search-form').submit(function(){
        flashMessage(localizer.processing, '', true);
        $('#search-submit').attr('disabled', true);
        var data = $(this).serializeArray();
        $.post('controller.php', {action:'search', search:data}, function(response){
            processResponse(response, '#track-list');
            if (response.html) {
                $('.closeable').hide();
            }
            $('#paging-action').val('');
            $('#search-submit').attr('disabled', false);
        });
        return false;
    });

    $('.toggle-button').live('click', function() {
        $('.closeable').toggle();
        return false;
    }).next().hide();

    $('#search-next').live('click', function(){
        $('#paging-action').val('next');
        $('#search-form').submit();
        return false;
    });

    $('#search-prev').live('click', function(){
        $('#paging-action').val('prev');
        $('#search-form').submit();
        return false;
    });
    // end search form actions
}

function setDateFields(){
    $('input.date').each(function() {
        var settings = {
            dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm:ss',
            showSeconds: true,
            showOn: 'both',
            buttonImage: g_admin_img + '/calendar_big.png',
            buttonImageOnly: true
        };
        $(this).datepicker(settings);
    });
}

function processResponse(response, selector) {
    hideMessage();
    if (typeof(response) == 'string') {
        window.open(g_admin_url + '/login.php?request=ajax', 'login', 'height=500,width=500');
        flashMessage(localizer.session_expired, 'error');
        return;
    }
    if (response.html) {
        $(selector).html(response.html);
    }
    if (response.js) {
        $(document).ready(function(){
            eval(response.js);
        });
    }
    if (response.message) {
        showMessage(response.message.title, response.message.text, response.message.type);
    }
}

function showMessage(title, message, type, fixed) {
    $('.status-message').detach();
    if (type) {
        var messageClass = type;
    } else {
        var messageClass = 'success';
    }
    var flash = $('<div class="status-message ' + messageClass + '"><h3>' + title + '</h3>' +
            '<p>' + message + '</p></div>')
    .appendTo('body')
    .css('z-index', '10000')
    .css('position', 'fixed')
    .css('top', '13px')
    .css('left', '33%')
    .css('width', '350px')
    .click(function() {
        $(this).detach();
    });

    if (!fixed) {
        flash.delay(4000).fadeOut('slow');
    }

    return flash;
}

function hideMessage() {
    $('.status-message').fadeOut('fast');
    $('.flash').fadeOut('fast');
}

function okCancel (title, message, func) {
    var buttons = {};
    buttons[localizer.cancel] = function(){$(this).dialog('close')};
    buttons[localizer.ok] = function(){func();$(this).dialog('close')};
    $('#dialog').detach();
    $('<div id="dialog" title="'+title+'">'+message+'</div>')
    .appendTo('body')
    .dialog({
        resizable: false,
        modal: true,
        buttons: buttons
    });
}
