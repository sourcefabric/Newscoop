<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/json2.js"></script>

<script type="text/javascript">
// Print last modified date
var dateTime = '<?php if ($savedToday) { p(date("H:i:s", $lastModified)); } else { p(date("Y-m-d H:i", $lastModified)); } ?>';
var fullDate = '<?php p(date("Y-m-d H:i:s", $lastModified)); ?>';
document.getElementById('info-text').innerHTML = '<?php putGS('Saved'); ?> ' + ' ' + dateTime;
document.getElementById('date-last-modified').innerHTML = '<?php putGS('Last modified'); ?> ' + ': ' + fullDate;

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
        flashMessage('<?php putGS('Article saved.'); ?>');
        if(save_and_close) {
            close(1);
        }
    } else {
        // tinymce should know that the current state is the correct one
        cleanTextContents();

        //fix breadcrumbs title
        $('.breadcrumbs li:last a').html($('#f_article_title').attr('value') + ' (' + $('#article_language').html() + ')');

         // ping for connection
        callServer('ping', [], function(json) {
            $.ajax({
                type: 'POST',
                url: '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/articles/post.php',
                data: form.serialize(),
                success: function(data, status, p) {
                    flashMessage('<?php putGS('Article saved.'); ?>');
                    toggleComments();
                    if(save_and_close) {
                        close(1);
                    }
                },
                error: function (rq, status, error) {
                    window.save_had_problems = true;
                    if (status == 0 || status == -1) {
                        flashMessage('<?php putGS('Unable to reach Newscoop. Please check your internet connection.'); ?>', 'error');
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
    $('form#article-keywords').submit();
    $('form#article-switches').submit();
    
    if ($(this).attr('id') == 'save_and_close') {
        if (articleChanged()) {
            unlockArticle();
            save_and_close = true;
            $('form#article-main').submit();
        } else {
            unlockArticle('close');
        }
    } else if ($(this).attr('id') == 'close') {
        callServer(['Article', 'setIsLocked'], [
        <?php echo $f_language_selected; ?>,
        <?php echo $articleObj->getArticleNumber(); ?>,
        0,
        <?php echo $g_user->getUserId(); ?>]);
        window.location.href = '<?php if ($f_publication_id > 0 && $f_issue_number > 0 && $f_section_number > 0) {
            echo "/$ADMIN/articles/index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_language_id=$f_language_id&f_section_number=$f_section_number";
        } else if ($f_publication_id > 0) {
            echo "/$ADMIN/pending_articles/index.php";
        } else {
            echo "/$ADMIN/";
        }
        ?>';
    } else if ($(this).attr('id') == 'save') {
        save_and_close = false;
        $('form#article-main').submit();
    }

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




var authorsList = [
<?php
$allAuthors = Author::GetAllExistingNames();
if ($userIsBlogger) {
    $blogInfo = $blogService->getBlogInfo($g_user);
    $allAuthors = array_map(function($author) {
        return $author->getName();
    }, ArticleAuthor::GetAuthorsByArticle($blogInfo->getArticleNumber(), $blogInfo->getLanguageId()));
}
$quoteStringFn = create_function('&$value, $key',
    '$value = json_encode((string) $value);');
array_walk($allAuthors, $quoteStringFn);
echo implode(",\n", $allAuthors);
?>
];

// authors autocomplete
$(".aauthor").autocomplete({
    source: authorsList
});
$(".aauthor").live('focus', function() {
    $(".aauthor").autocomplete({
        source: authorsList
    });
});

// fancybox for popups
$('a.iframe').each(function() {
    if (!$(this).attr('custom')) {
        $(this).fancybox({
            hideOnContentClick: false,
            width: 660,
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

window.article_confirm_question = '<?php putGS('Your work has not been saved. Do you want to continue and lose your changes?'); ?>';

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
                return confirm('<?php putGS('Please be aware that all scheduled publishing events for this article will be deleted when you set this article to "New" state. Please confirm the state change.'); ?>');
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

        var leave_links = ['/admin', '/admin/auth/logout', '/admin/application/help'];
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
            injectHtml += '<li>'+item.title+'</li>';
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
	var url = '<?php echo $Campsite['WEBSITE_URL']; ?>/admin/multidate/getdates';
    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        data: {
            articleId : "<?php echo Input::Get('f_article_number', 'int', 1)?>"
        },
        success: function(data) {

        	var eventList = '';
        	eventList += '<ul class="block-list">';
            
            for(var i=0; i<data.length; i++) {
                if (i >= 20 ) {
                    break;
                }
                var item = data[i];
                
                var start = new Date(item.start * 1000);                
                var minutes = start.getMinutes();
                if (minutes < 10) {
                    minutes = '0' + minutes;
                }
                var month = ( start.getMonth() + 1 );
                var startString = (month + '/' + start.getDate() + '/' + start.getFullYear() + ' ' + start.getHours() + ':' + minutes );

                var end = new Date(item.end * 1000);
                var minutes = end.getMinutes();
                if (minutes < 10) {
                    minutes = '0' + minutes;;
                }
                var month = ( end.getMonth() + 1 );
                var endString = (month + '/' + end.getDate() + '/' + end.getFullYear() + ' ' + end.getHours() + ':' + minutes );

                var eventString = startString + ' - ' + endString;
                eventList += '<li>' + eventString + '</li>';
            }
            $('#multiDateEventList').html('');
            $('#multiDateEventList').append(eventList + '</ul>');              
        }
        
    });    
}

</script>
