<?php
  $translator = \Zend_Registry::get('container')->getService('translator');
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/json2.js"></script>
<script type="text/javascript">
// Print last modified date
var dateTime = '<?php if ($savedToday) { p(date("H:i:s", $lastModified)); } else { p(date("Y-m-d H:i", $lastModified)); } ?>';
var fullDate = '<?php p(date("Y-m-d H:i:s", $lastModified)); ?>';
document.getElementById('info-text').innerHTML = '<?php echo $translator->trans('Saved', array(), 'articles'); ?> ' + ' ' + dateTime;
document.getElementById('date-last-modified').innerHTML = '<?php echo $translator->trans('Last modified', array(), 'articles'); ?> ' + ': ' + fullDate;

/**
 * Close window after timeout
 * @param int timeout
 * @return void
 */
var close = function(timeout) {
    setTimeout("window.location.href = '<?php
    if ($f_publication_id > 0 && $f_issue_number > 0 && $f_section_number > 0) {
        echo "/$ADMIN/articles/index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_language_id=$f_language_id&f_section_number=$f_section_number";
    } else if ($f_publication_id > 0) {
        echo "/$ADMIN/pending_articles/index.php";
    } else {
        echo "/$ADMIN/";
    }
    ?>'", timeout);
};

$(function() {

// make breadcrumbs + save buttons sticky
$('.breadcrumb-bar, .toolbar').wrapAll('<div class="sticky" />');

// datepicker for date
$('.datepicker').datepicker({
    dateFormat: 'yy-mm-dd'
});

// accordion hovers
$('.ui-accordion-header').hover(
    function(){ $(this).removeClass('ui-state-default').addClass('ui-state-hover'); },
    function(){ $(this).removeClass('ui-state-hover').addClass('ui-state-default'); }
);

// hover states on the static widgets
$('.icon-button').hover(
    function() { $(this).addClass('ui-state-hover'); },
    function() { $(this).removeClass('ui-state-hover'); }
);

$('.collapsible').each(function(index) {
    var head = $('> .head', $(this));
    var cookie = 'articlebox-' + index;
    var closed = $.cookie(cookie);
    var expires = { expires: 14 } // 14 days cookie expiration

    // init by cookie
    if (closed != 1) {
        head.addClass('ui-state-active');
    } else {
        $(this).next().hide();
    }

    // toggle
    $(this).click(function() {
        $(this).next().toggle('fast');
        head.toggleClass('ui-state-active');
        if (head.hasClass('ui-state-active')) {
            $.cookie(cookie, 0, expires);
        } else {
            $.cookie(cookie, 1, expires);
        }
        return false;
    });
});

// copy title to hidden field
$('input:text[name=f_article_title]').change(function() {
    $('input:hidden[name=f_article_title]').val($(this).val())
        .closest('form').change();
}).change();

/**
 * Enable/disable comments list/form according to selected state.
 */
var toggleComments = function() {
    $('input:radio[name^="f_comment"]:checked').each(function() {
        var form = $('#comments-form');
        var list = $('#comments-list');
        var commentReply = $('#comment-moderate dd.buttons');
        switch ($(this).val()) {
            case 'enabled':
                form.show();
                list.show();
                commentReply.show();
                break;

            case 'disabled':
                form.hide();
                list.hide();
                break;

            case 'locked':
                form.hide();
                list.show();
                commentReply.hide();
                break;
        }
    });
};

// init
toggleComments();

/**
 * Telling to the Tinymce that the current state is the correct one
 */
cleanTextContents = function()
{
    var editor_rank = 0;
    while (true) {
        var editor_obj = typeof(tinyMCE) != "undefined" ? tinyMCE.get(editor_rank) : false;
        if (!editor_obj) {
            break;
        }
        editor_obj.isNotDirty = true;
        editor_rank += 1;
    }
};

/**
 * Tracking save problems is used at checking un/saved state of article
 */
window.save_had_problems = false;
window.ajax_had_problems = false;

// main form submit
$('form#article-main').submit(function() {
    window.save_had_problems = false;
    var form = $(this);
    if (!articleChanged()) {
        flashMessage('<?php echo $translator->trans('Article saved.', array(), 'articles'); ?>');
        if(save_and_close) {
            close(1);
        }
    } else {
        // tinymce should know that the current state is the correct one
        cleanTextContents();

        //fix breadcrumbs title
        $('.breadcrumbs li:last a').text($('#f_article_title').val() + ' (' + $('#article_language').text() + ')');

         // ping for connection
        callServer('ping', [], function(json) {
            $.ajax({
                type: 'POST',
                url: '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/articles/post.php',
                data: form.serialize(),
                success: function(data, status, p) {
                    flashMessage('<?php echo $translator->trans('Article saved.', array(), 'articles'); ?>');
                    toggleComments();
                    if(save_and_close) {
                        close(1);
                    }
                },
                error: function (rq, status, error) {
                    window.save_had_problems = true;
                    if (status == 0 || status == -1) {
                        flashMessage('<?php echo $translator->trans('Unable to reach Newscoop. Please check your internet connection.', array(), 'articles'); ?>', 'error');
                    }
                }
            });

        }); // /ping
        $(this).removeClass('changed');
    }

    return false;
}).change(function() {
    $(this).addClass('changed');
});

/**
 * Unlock article
 * @return void
 */
function unlockArticle(doAction) {
    doAction = typeof(doAction) != 'undefined' ? doAction : 'none';
    callServer(['Article', 'setIsLocked'], [
        <?php echo $f_language_selected; ?>,
        <?php echo $articleObj->getArticleNumber(); ?>,
        0,
        <?php echo $g_user->getUserId(); ?>], function() {
           if(doAction == 'close') {
                close(1);
           }
        });
};

<?php if ($inEditMode) { ?>

// save all buttons

$('.save-button-bar input').click(function() {
    if ($(this).attr('id') == 'save_and_close') {
        if (articleChanged()) {
            unlockArticle();
            save_and_close = true;
            $('form#article-main').submit();
        } else {
            unlockArticle('close');
        }
    } else if ($(this).attr('id') == 'close') {
        var redirect = '<?php if ($f_publication_id > 0 && $f_issue_number > 0 && $f_section_number > 0) {
                                echo "/$ADMIN/articles/index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_language_id=$f_language_id&f_section_number=$f_section_number";                              } else if ($f_publication_id > 0) {
                                echo "/$ADMIN/pending_articles/index.php";
                              } else {
                                echo "/$ADMIN/";
                              } ?>'; 
        callServer(['Article', 'setIsLocked'], [
        <?php echo $f_language_selected; ?>,
        <?php echo $articleObj->getArticleNumber(); ?>,
        0,
        <?php echo $g_user->getUserId(); ?>], function() {
            window.location.href = redirect;
        });
        return false;
    } else if ($(this).attr('id') == 'save') {
        save_and_close = false;
        $('form#article-main').submit();
    }

    $('form#article-keywords').submit();
    $('form#article-switches').submit();

    return false;
});

<?php } else { // view mode ?>
$('.save-button-bar input#save_and_close').click(function() {
<?php if ($articleObj->isLocked() && $articleObj->getLockedByUser() == $g_user->getUserId()) { ?>
    unlockArticle();
<?php } ?>
    close(1);
});
<?php } ?>



<?php
$allAuthors = array();
if ($userIsBlogger) {
    $blogInfo = $blogService->getBlogInfo($g_user);
    $allAuthors = array_map(function($author) {
        return $author->getName();
    }, ArticleAuthor::GetAuthorsByArticle($blogInfo->getArticleNumber(), $blogInfo->getLanguageId()));

$quoteStringFn = create_function('&$value, $key',
    '$value = json_encode((string) $value);');
array_walk($allAuthors, $quoteStringFn);
?>
var authorsList = [<?php echo implode(",\n", $allAuthors); ?>];

$(".aauthor").autocomplete({ source: authorsList });

<?php } else { ?>
// authors autocomplete
$(".aauthor").live('focus', function() {
    $(".aauthor").autocomplete({
      source: function (request, response) {
        $.ajax({
          url: "/admin/authors/get",
          dataType: "json",
          data: {
            limit: 10,
            term: request.term,
            users: true
          },
          success: function (data) {
            response($.map(data, function(item) {
              return {
                label: item.name,
                value: item.name
              }
            }));
          }
        });
      },
      minLength: 1,
      open: function () {
        $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
      },
      close: function () {
        $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
      }
    });
});

<?php } ?>


// fancybox for popups
$('a.iframe').each(function() {
    if (!$(this).attr('custom')) {
        $(this).fancybox({
            hideOnContentClick: false,
            width: 800,
            height: 500,
            onStart: function() { // check if there are any changes
                return checkChanged();
            },
            onClosed: function(url, params) {
                if ($.fancybox.reload) { // reload if set
                    if ($.fancybox.message) { // set message after reload
                        $.cookie('flashMessage', $.fancybox.message);
                    }
                    window.location.reload();
                } else if ($.fancybox.error) {
                    flashMessage($.fancybox.error, 'error');
                } else {
                    window.location.reload();
                }
            }
        });
    }
});

$('#locations_box a.iframe').each(function() {
    $(this).data('fancybox').showCloseButton = false;
    $(this).data('fancybox').width = 1100;
    $(this).data('fancybox').height = 660;

});

$('#place-images').fancybox({
    hideOnContentClick: false,
    width: 1300,
    height: 800,
    type: 'iframe'
});

$('#attach-images').fancybox({
    hideOnContentClick: false,
    width: 1300,
    height: 800,
    type: 'iframe',
    onStart: function() { // check if there are any changes
        return checkChanged();
    },
    onClosed: function() {
        window.location.reload();
    }
});

$('#edit-images').fancybox({
    hideOnContentClick: false,
    width: 1300,
    height: 800,
    type: 'iframe',
    onStart: function() { // check if there are any changes
        return checkChanged();
    },
    onClosed: function() {
        window.location.reload();
    }
});

$('#topic_box_frame a.iframe').each(function() {
    $(this).data('fancybox').width = 1200;
});

$("#context_box a.iframe").fancybox({
    'showCloseButton' : false,
    'width': 1150,
    'height'     : 700,
    'scrolling' : 'auto',
    'onClosed'      : function() {
       loadContextBoxActileList();
    }
});

$("#multidate_box a.iframe").fancybox({
    'showCloseButton' : false,
    'width': 1000,
    'height'     : 710,
    'scrolling' : 'auto',
    'onClosed'      : function() {
       //loadContextBoxActileList();
       loadMultiDateEvents();
    }
});

$("#playlist a.iframe").fancybox
({
    'showCloseButton' : false,
    'type' : 'iframe',
    'width' : 700,
    'height' : 700,
    'scrolling' : 'auto',
    'autoDimensions' : true
});

// comments form check for changes
$('form#article-comments').submit(function() {
    if (!checkChanged()) {
        return false;
    }
});

var message = $.cookie('flashMessage');
if (message) {
    flashMessage(message);
    $.cookie('flashMessage', null);
}

}); // /document.ready

/**
 * Check for unsaved changes in tinymce editors
 * @return bool
 */
function editorsChanged()
{
    var editor_rank = 0;
    while (true) {
        var editor_obj = typeof(tinyMCE) != "undefined" ? tinyMCE.get(editor_rank) : false;
        if (!editor_obj) {
            break;
        }
        if (editor_obj.isDirty()) {
            return true;
        }
        editor_rank += 1;
    }

    return false;
};

/**
 * Check for unsaved changes in main/boxes forms
 * @return bool
 */
function articleChanged()
{
    if (window.save_had_problems || window.ajax_had_problems) {
        return true;
    }

    if ((!editorsChanged()) && ($('form.changed').size() == 0)) {
        return false;
    }

    return true;
};

window.article_confirm_question = '<?php echo $translator->trans('Your work has not been saved. Do you want to continue and lose your changes?', array(), 'articles'); ?>';

/**
 * Check for unsaved changes in main/boxes forms
 * Asks for confirmations too.
 * @return bool
 */
function checkChanged()
{
    if( $("#f_action_workflow").val() == 'N' ) {
        <?php
            if ( count($articleEvents) ) {
                ?>
                return confirm('<?php echo $translator->trans('Please be aware that all scheduled publishing events for this article will be deleted when you set this article to New state. Please confirm the state change.', array(), 'articles'); ?>');
                <?php
            }
        ?>
    }
    if (!articleChanged()) {
        return true; // continue
    }
    return confirm(window.article_confirm_question);
}

/**
 * Check for unsaved changes in main/boxes forms
 * Warn if leaving the page without saving it.
 */
$(document).ready(function() {
    window.onbeforeunload = function ()
    {
        if (articleChanged())
        {
            return window.article_confirm_question;
        }
    };
    if (window.opera) {
        $('.breadcrumbs').click(function() {
            if (articleChanged()) {
                return confirm(window.article_confirm_question);
            }
            return true;
        });

        var leave_links = ['/admin', '/admin/logout', '/admin/application/help'];
        var leave_links_length = leave_links.length;

        for (var lind = 0; lind < leave_links_length; lind++) {
            var one_leaf = leave_links[lind];

            $('a[href$="' + one_leaf + '"]').click(function() {
                if (articleChanged()) {
                    return confirm(window.article_confirm_question);
                }
                return true;
            });
        }
    }
    loadContextBoxActileList();
    loadMultiDateEvents();
});

function fnLoadContextBoxArticleList(data) {
    var items = data.items;
    if(items.length > 0) {
        var injectHtml = '<ul class="block-list">';
        for(var i=0; i<items.length; i++) {
            var item = items[i];
            injectHtml += '<li>';
            injectHtml += $('<li />').text(item.title).html();
            injectHtml += '</li>';
        }
        injectHtml += '</ul>';
        $("#contextBoxArticlesList").html(injectHtml);
    } else  {
        $("#contextBoxArticlesList").html('');
    }
}

function loadContextBoxActileList() {
    var aoData = new Array();
    var items = new Array('1_1','0_0');

    aoData.push("context_box_load_list");
    aoData.push(items);
    aoData.push({
        'articleId': '<?php echo Input::Get('f_article_number', 'int', 1)?>',
    });
    callServer(['ArticleList', 'doAction'], aoData, fnLoadContextBoxArticleList);
}

function loadMultiDateEvents() {
    if ((window.has_multidates === undefined) || (!window.has_multidates)) {
        return;
    }

<?php
    $f_language_id = Input::Get('f_language_id', 'int', 1);
    $f_language_selected = (int)camp_session_get('f_language_selected', 0);

    $article_language_use = $f_language_selected;
    if (empty($article_language_use)) {
        $article_language_use = $f_language_id;
    }
?>

    var url = '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/multidate/getdates';
    callServer(
        {
            method: 'GET',
            url: url
        },
        {
            articleId : "<?php echo Input::Get('f_article_number', 'int', 1)?>",
            languageId : "<?php echo $article_language_use; ?>"
        },
        function(data) {
            var eventList = '';
            eventList += '<ul class="block-list">';

            var dispalyed_all = true;
            
            for(var i=0; i<data.length; i++) {
                if (i >= 20 ) {
                    dispalyed_all = false;
                    break;
                }
                var item = data[i];
                
                var start = new Date(item.start_utc * 1000);                
                var end = new Date(item.end_utc * 1000);

                var start_values = {
                    'month': start.getUTCMonth() + 1,
                    'day': start.getUTCDate(),
                    'hour': start.getUTCHours(),
                    'minute': start.getUTCMinutes()
                };
                var end_values = {
                    'month': end.getUTCMonth() + 1,
                    'day': end.getUTCDate(),
                    'hour': end.getUTCHours(),
                    'minute': end.getUTCMinutes()
                };
                for (var start_key in start_values) {
                    if (start_values[start_key] < 10) {
                        start_values[start_key] = '0' + start_values[start_key];
                    }
                }
                for (var end_key in end_values) {
                    if (end_values[end_key] < 10) {
                        end_values[end_key] = '0' + end_values[end_key];
                    }
                }

                if (item.allDay) {
                    end_values['hour'] = '24';
                    end_values['minute'] = '00';
                }
                if ((item.restOfDay !== undefined) && item.restOfDay) {
                    end_values['hour'] = '24';
                    end_values['minute'] = '00';
                }

                var startString = '<span style="float:left">' + (start.getUTCFullYear() + '-' + start_values['month'] + '-' + start_values['day'] + ' ' + start_values['hour'] + ':' + start_values['minute'] ) + '</span>';
                var endString = '<span style="float:left">' + (end.getUTCFullYear() + '-' + end_values['month'] + '-' + end_values['day'] + ' ' + end_values['hour'] + ':' + end_values['minute'] ) + '</span>';

                var eventString = startString + '<span style="float:left" class="ui-icon ui-icon-arrowthick-1-e"></span>' + endString;

                var event_comment = item.event_comment;
                if (null === event_comment) {
                    event_comment = '';
                }
                event_comment = event_comment.replace('"', "&quot;")
                event_comment = event_comment.replace("'", "&apos;")
                eventList += '<li style="background-color:'+item.backgroundColor+'; color:'+item.textColor+';" title="' + event_comment + '"><span style="float:right">' + item.field_name + '</span>' + eventString + '</li>';
            }

            var other_notice = '';
            if (!dispalyed_all) {
                other_notice = '<li><span class="ui-icon ui-icon-grip-dotted-horizontal"></span></li>';
            }

            $('#multiDateEventList').html('');
            $('#multiDateEventList').append(eventList + other_notice + '</ul>');              
        },
        true
    );    
}

</script>